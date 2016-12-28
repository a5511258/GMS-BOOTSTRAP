<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16/7/19
 * Time: 下午4:04
 */

namespace Service\Utils;


class GroupInfoUtils
{
    //连接数据库取出GroupInfo数据
    public function getGroup($groupId = 1)
    {
        $dao = D('group_info');//连接数据库
        $data = $dao->where(array(
            'group_id' => $groupId,
        ))->find(); //查找语句
        $result = array();
        if ($data) {//判断有数据
            $parent = $data;//父节点
            $daoP = D('group_info');
            $map['id_level'] = array('LIKE', $parent['id_level'] . '%');
            $dataP = $daoP->where($map)->select();
            foreach ($dataP as $k => $groupInfo) {
                $groupInfo['parent_name'] = "";
                $data_PN = $daoP->where(array(
                    'group_id' => $groupInfo['parent_id']
                ))->find();
                $dataP[$k]['parent_name'] = $data_PN['group_name'];
            }
            $result = $dataP;
        }
        return $result;
    }

    //整理数据库传递数据格式
    public function formatarray($array)
    {
        $soucearray = array();
        for ($i = 0; $i < count($array); $i++) {
            $temp = array();
            $temp['id'] = $array[$i]['group_id'];
            $temp['text'] = $array[$i]['group_name'];
            $temp['ParentId'] = $array[$i]['parent_id'];
            if (0 == $i) $temp['state'] = "closed";
            $temp['attributes']['type'] = "group";
            $temp['attributes']['id_level'] = $array[$i]['id_level'];;
            $temp['iconCls'] = "icon-folder";//此处添加图标后会影响用户界面
            array_push($soucearray, $temp);
        }
        if (1 == count($array)) {
            unset($soucearray[0]['state']);
        }
        return $soucearray;
    }


    function getGroupName($group_id)
    {
        $dao = D('group_info');//连接数据库
        $data = $dao->where(array(
            'group_id' => $group_id,
        ))->getField('group_name'); //查找语句
        return $data;
    }

    function getGroupType($group_id){
        $dao = D('group_info');//连接数据库
        $data = $dao->where(array(
            'group_id' => $group_id,
        ))->getField('group_type'); //查找语句
        return $data;
    }


    function getParentGroupID($group_id)
    {
        $dao = D('group_info');//连接数据库
        $data = $dao->where(array(
            'group_id' => $group_id,
        ))->find(); //查找语句
    }


    /**
     * 通过$group_ids 查找所有下属ID
     * @param $group_ids        欲查找下属组织ID字符串
     * @return string           查找结果
     */
    function getAllGroupID($group_ids, $is_str = true)
    {
        $array_group_ids = array();
        $temp_group_id = explode(",", $group_ids);
        $Utils = new GroupInfoUtils();
        foreach ($temp_group_id as $v) {
            $dataG = $Utils->getGroup($v);
            if ($dataG) {
                for ($x = 0; $x < count($dataG); $x++) {
                    array_push($array_group_ids, $dataG[$x]['group_id']);
                }
            }
        }
        $array_group_ids = array_unique($array_group_ids);
        if ($is_str) {
            return implode(',', $array_group_ids);
        } else {
            return $array_group_ids;
        }
    }


    public function Is_AuthUtils()
    {

        $result = array();
        $result['edit'] = false;
        $result['del'] = false;
        if (CONTROLLER_NAME == "GroupInfo") {
            if (Is_Auth("Service/GroupInfo/Edit")) {
                $result['edit'] = true;
            }
            if (Is_Auth("Service/GroupInfo/Delete")) {
                $result['del'] = true;
            }
        }
        if (CONTROLLER_NAME == "DeviceInfo") {
            if (Is_Auth("Service/DeviceInfo/Edit")) {
                $result['edit'] = true;
            }
            if (Is_Auth("Service/DeviceInfo/Delete")) {
                $result['del'] = true;
            }
        }  if (CONTROLLER_NAME == "SimCardInfo") {
            if (Is_Auth("Service/SimCardInfo/Edit")) {
                $result['edit'] = true;
            }
            if (Is_Auth("Service/SimCardInfo/Delete")) {
                $result['del'] = true;
            }
        }
        if (CONTROLLER_NAME == "VehicleInfo") {
            if (Is_Auth("Service/VehicleInfo/Edit")) {
                $result['edit'] = true;
            }
            if (Is_Auth("Service/VehicleInfo/Delete")) {
                $result['del'] = true;
            }
        }
        if (CONTROLLER_NAME == "VehicleRepairInfo") {
            if (Is_Auth("Service/VehicleRepairInfo/Edit")) {
                $result['edit'] = true;
            }
            if (Is_Auth("Service/VehicleRepairInfo/Delete")) {
                $result['del'] = true;
            }
        }
        if (CONTROLLER_NAME == "VehicleMaintenanceInfo") {
            if (Is_Auth("Service/vehicleViolationInfo/Edit")) {
                $result['edit'] = true;
            }
            if (Is_Auth("Service/VehicleRepairInfo/Delete")) {
                $result['del'] = true;
            }
        }
        if (CONTROLLER_NAME == "VehicleViolationInfo") {
            if (Is_Auth("Service/VehicleViolationInfo/Edit")) {
                $result['edit'] = true;
            }
            if (Is_Auth("Service/VehicleViolationInfo/Delete")) {
                $result['del'] = true;
            }
        }
        if (CONTROLLER_NAME == "VehicleInsuranceInfo") {
            if (Is_Auth("Service/VehicleInsuranceInfo/Edit")) {
                $result['edit'] = true;
            }
            if (Is_Auth("Service/VehicleInsuranceInfo/Delete")) {
                $result['del'] = true;
            }
        }
        if (CONTROLLER_NAME == "VehicleRecordInfo") {
            if (Is_Auth("Service/VehicleRecordInfo/Edit")) {
                $result['edit'] = true;
            }
            if (Is_Auth("Service/VehicleRecordInfo/Delete")) {
                $result['del'] = true;
            }
        }
        if (CONTROLLER_NAME == "VehicleRoadTollInfo") {
            if (Is_Auth("Service/VehicleRoadTollInfo/Edit")) {
                $result['edit'] = true;
            }
            if (Is_Auth("Service/VehicleRoadTollInfo/Delete")) {
                $result['del'] = true;
            }
        }
        if (CONTROLLER_NAME == "VehicleOilConsumptionInfo") {
            if (Is_Auth("Service/VehicleOilConsumptionInfo/Edit")) {
                $result['edit'] = true;
            }
            if (Is_Auth("Service/VehicleOilConsumptionInfo/Delete")) {
                $result['del'] = true;
            }
        }
        return $result;


    }

}