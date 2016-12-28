<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16/8/31
 * Time: 下午4:27
 */

namespace Service\Model;


use Gps\Model\BaiduConvertModel;
use Gps\Model\KeDaOnlineModel;
use Gps\Model\LastGpsModel;
use Service\Utils\GroupInfoUtils;
use Think\Exception;
use Think\Model;

class VehicleTreeModel
{
    const OFFLINE_LIMIT=600;
    public function __construct()
    {
        $this->groupInfoModel = new GroupInfoModel();
    }

    public function getSummaryData()
    {

    }

    public function getTree()
    {
        $currentTime=time();
        $vehicleGroupInfoViewModel = new VehicleGroupInfoViewModel();
        $searchVehicleWhere = $this->buildWhere();
        $vehicles = $vehicleGroupInfoViewModel->where($searchVehicleWhere)->select();
        $vehicleIds=array();
        $groupCountInfo=array();//组中车辆数量信息

        //获取科达在线信息
        $kedaOnlineModel= new KeDaOnlineModel();
        try{
            $onlineCars=$kedaOnlineModel->getOnlineCars(true);
        }catch (Exception $e)
        {
            $onlineCars=array();
        }
        foreach ($vehicles as $vehicle) {
            $vehicleIds[]=$vehicle["vehicle_id"];
        }
        //获取最近gps信息
        $lastGpsModel=new LastGpsModel();
        $gpsWhere["vehicle_id"]=array("in",$vehicleIds);
        $lastGpsData=parseToIndex($lastGpsModel->where($gpsWhere)->select(),"vehicle_id");
        //百度坐标转换
        $lastGpsData= BaiduConvertModel::convertAllToBaidu($lastGpsData);
        //        车辆数据格式化
        foreach ($vehicles as $key=>$vehicle) {
            $id=$vehicle["vehicle_id"];
            $group_id=$vehicle["group_id"];
            if (isset($onlineCars[$id]))
            {
                $onlineCarData= $onlineCars[$id];
                foreach ($onlineCarData["channel"] as $channel) {
                    $attributes["index"]=$channel["index"];
                    $attributes["type"]="car_camera";
                    $attributes["vehicle_id"]=$id;
                    $returnChannel["id"]=-1;
                    $returnChannel["iconCls"]="icon-car_camera";
                    $returnChannel["text"]="通道".($channel["index"]+1);
                    $returnChannel["attributes"]=$attributes;
                    $vehicle["children"][]=$returnChannel;
                }
                $vehicle["camera_count"]=count($onlineCarData["channel"]);
                $vehicle["is_online"]=true;
            }
            if (isset($lastGpsData[$id]))
            {
                $lastGpsItem= $lastGpsData[$id];
                if ($currentTime-strtotime($lastGpsItem["time"])<self::OFFLINE_LIMIT)
                {
                    if ($vehicle["vehicle_from"]!=VEHICLE_FROM_KEDA)//如果不是科达平台,使用gps做在线判断
                    {
                        $vehicle["is_online"]=true;
                    }
                }
                $vehicle["gps"]=$lastGpsItem;
            }
            $this->checkGroupCountItem($groupCountInfo, $group_id);
            if (isset($vehicle["is_online"])&&$vehicle["is_online"]){
                $groupCountInfo[$group_id]["online"]+=1;
            }else{
                $vehicle["is_online"]=false;
            }
            $groupCountInfo[$group_id]["total"]+=1;
            $vehicles[$key]=$vehicle;
        }

        //对数据进行排序，按照是否在线
        $this->sortArrByField($vehicles,"is_online",true);

        //获取当前用户的所有子组织
        $groupInfos = $this->groupInfoModel->getGroupInfos(GroupInfoModel::getSubGroupId());
        //按组统计车
        foreach ($groupCountInfo as $group_id => $countItem) {
            $groupInfo = $groupInfos[$group_id];
            $parentIds = parseParentIdLevel($groupInfo["id_level"],false);
            foreach ($parentIds as $parentId) {
                $this->checkGroupCountItem($groupCountInfo, $group_id);
                $groupCountInfo[$parentId]["online"] += $countItem["online"];
                $groupCountInfo[$parentId]["total"] += $countItem["total"];
            }
        }
        $returnGroups = array();
        //去除无车组
        foreach ($groupInfos as $groupId => $groupInfo) {
            if (isset($groupCountInfo[$groupId]["total"]) && $groupCountInfo[$groupId]["total"] > 0) {
                $attributes=array();
                $attributes["type"] = "group";
                $attributes["name"] = $groupInfo["group_name"];
                $attributes["id_level"] = $groupInfo["id_level"];
                $groupInfoItem["id"] = $groupInfo["group_id"];
                $groupInfoItem["text"] = $groupInfo["group_name"]."(".$groupCountInfo[$groupId]["online"]."/".$groupCountInfo[$groupId]["total"].")";
                $groupInfoItem["ParentId"] = $groupInfo["parent_id"];
                $groupInfoItem["iconCls"] = "icon-organization";
                $groupInfoItem["state"] = "closed";
                $groupInfoItem["attributes"] = $attributes;
                $returnGroups[$groupId] = $groupInfoItem;
            }
        }
        foreach ($vehicles as $vehicle) {
            $vehicleItem=array();
            $attributes=array();
            $attributes["type"] = "car";
//            $attributes[ "id_level"]="car";
            if (isset($vehicle["children"]))//
            {
                if (count($vehicle["children"])>1)
                {
                    $vehicleItem["children"]=$vehicle["children"];
                }
                $vehicleItem["camera_info"]=$vehicle["children"];
                unset($vehicle["children"]);
            }
            $attributes["carinfo"] = $vehicle;
            $attributes["gps_info"] = $vehicle["gps"];
            unset($vehicle["gps"]);
            $vehicleItem["id"] = $vehicle["vehicle_id"] + 1000000000;
            $vehicleItem["text"] = $vehicle["vehicle_license"];
            $groupInfoItem["state"] = "closed";
            $vehicleItem["attributes"] = $attributes;
            if ($vehicle["is_online"])
            {
                $vehicleItem["iconCls"] = "icon-car_online";
            }else{
                $vehicleItem["iconCls"] = "icon-car_offline";
            }
            $vehicleItem["ParentId"] = $vehicle["group_id"];
            $returnGroups[$vehicle["group_id"]]["children"][] = $vehicleItem;
        }
        $rootPid = $returnGroups[getCurrentGroupId()]["ParentId"];
        $needReturn = list_to_tree($returnGroups, "id", "ParentId", "children", $rootPid);
        return $needReturn;
    }

    /**
     * 搜索当前用户组织下  包含组织名的 的所有子组织
     * @param $value
     * @param $searchGroupWhere
     * @param $group_ids
     * @param $groupInfoModel
     * @return mixed
     */
    protected function searchGroupAsGroupId($value = "")
    {
        $group_ids=GroupInfoModel::getSubGroupId();
        //去除当前用户所有下属组织信息
        if ($value) {
            $Utils = new GroupInfoUtils();
            //搜索所有组织
            $searchGroupWhere["group_name"] = array('like', "%$value%");
            $searchGroupWhere["group_id"] = array('in', $group_ids);
            $searchGroupIds = $this->groupInfoModel->where($searchGroupWhere)->getField("group_id", true);
            if ($searchGroupIds) {
                $newSearchGroupIds = array();
                foreach ($searchGroupIds as $ids) {
                    $newSearchGroupIds = array_merge($newSearchGroupIds, $Utils->getAllGroupID($ids, false));
                }
                $group_ids = array_intersect(explode(",", $group_ids), $newSearchGroupIds);
                return $group_ids;
            }
        }

        return $group_ids;
    }

    /**
     * 根据http传入参数，创建相应的where查询条件. 会自动搜素组织名
     * @param $group_ids
     * @param $where
     * @return mixed
     */
    protected function buildWhere()
    {
        $columnName=I("columnName");
        $value=I("value");
        $where = array();
        if ($columnName == "group_name") {
            $where["group_id"]= array("in",$this->searchGroupAsGroupId($value));
        }else{
            $where["group_id"]= array("in",$this->searchGroupAsGroupId());
            $where[$columnName]=array('like', "%$value%");
        }
        return $where;
    }

    /**
     * @param $groupCountInfo
     * @param $group_id
     * @return mixed
     */
    protected function checkGroupCountItem(&$groupCountInfo, $group_id)
    {
        if (!isset($groupCountInfo[$group_id])) {
            $groupCountInfo[$group_id]["online"] = 0;
            $groupCountInfo[$group_id]["total"] = 0;
        }
    }
    function sortArrByField(&$array, $field, $desc = false){
        $fieldArr = array();
        foreach ($array as $k => $v) {
            $fieldArr[$k] = $v[$field];
        }
        $sort = $desc == false ? SORT_ASC : SORT_DESC;
        array_multisort($fieldArr, $sort, $array);
    }
}