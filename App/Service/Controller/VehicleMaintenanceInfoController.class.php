<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16/9/22
 * Time: 下午4:08
 */

namespace Service\Controller;


use Service\Model\GroupInfoModel;
use Service\Model\VehicleGroupInfoViewModel;
use Service\Model\VehicleMaintenanceViewModel;
use Think\Controller;
use Service\Utils\GroupInfoUtils;

class VehicleMaintenanceInfoController extends BaseSearchController
{
    function index()
    {
        $Utils = new GroupInfoUtils();
        $result = $Utils->Is_AuthUtils();
        $this->assign('edit', $result['edit']);
        $this->assign('del', $result['del']);
        $this->display();
    }

    /**
     * 获取当前登陆用户可以加载的车牌列表
     */
    public function getVehicleLicenseList()
    {
        $dao_vehicle = D('vehicle_group_info_view');
        $Utils = new GroupInfoUtils();
        $group_id = session('UserInfo')['service_group_id'];
        $group_ids = $Utils->getAllGroupID($group_id);
        $map['group_id'] = array('in', $group_ids);
        $data = $dao_vehicle->where($map)->field("vehicle_id as word_id,vehicle_license as word")->select();
        echo json_encode($data);
    }


    public function getVehicleMaintenanceInfoToList($startTime = "", $endTime = "", $vehicle_license = "")
    {
        $maintenanceViewModel = new VehicleMaintenanceViewModel();
        $where = array();
        if ($startTime && $endTime) {
            $where["maintenance_time"] = array('between', array($startTime . " 00:00:00", $endTime . " 23:59:59"));
        }
        if ($vehicle_license) {
            $where["vehicle_license"] = array('like', '%' . $vehicle_license . '%');
        }
        $group_ids = GroupInfoModel::getSubGroupId();
        if ($group_ids) {
            $where["group_id"] = array('in', $group_ids);
        }
        $this->executeSearch($maintenanceViewModel, $where);
    }


    public function Create()
    {
        //数据未校验
        $post_data = I('post.');

        $dao = D('vehicle_maintenance_info');
        $dao->startTrans();
        $res = $dao->add($post_data);
        if (false !== $res) {
            $dao->commit();

            action_log('User_Add', MODULE_NAME, session('UserInfo')['service_group_id']);

            returnSuccess('操作成功!');
        } else {
            $dao->rollback();

            returnError('操作失败!');
        }

    }

    public function Update()
    {
        $post_data = I('post.');

        $dao = D('vehicle_maintenance_info');
        $id = $post_data['id'];
        $vehicle_id = $post_data['vehicle_id'];
        unset($post_data['id']);
        unset($post_data['vehicle_id']);
        $dao->startTrans();
        $res = $dao->where(array(
            'vehicle_id' => $vehicle_id,
            'id' => $id
        ))->save($post_data);
        if (false !== $res) {

            $dao->commit();
            action_log('User_Edit', MODULE_NAME, session('UserInfo')['service_group_id']);
            returnSuccess('操作成功!');
        } else {
            $dao->rollback();
            returnError('操作失败!');
        }


    }

    public function Delete()
    {
        $result = array();
        $result['Code'] = 200;
        $result['Msg'] = "删除失败！";
        $result['Result'] = false;
        $delcount = 0;
        if (IS_POST) {
            $postdatas = I('post.');

            $dao = D('vehicle_maintenance_info');//连接数据库

            if ($n = $dao->where(array('vehicle_id' => $postdatas['VehicleId'], 'id' => $postdatas['id']))->delete()) {
                $delcount++;
            }
            if ($delcount > 0) {
                if (1 == $delcount) {
                    $result['Msg'] = "删除成功!";
                } else {
                    $result['Msg'] = "共删除了" . $delcount . "条数据";
                }
                $result['Result'] = true;
            } else {
                $result['Msg'] = "删除失败";
            }
            action_log('User_Del', MODULE_NAME, session('UserInfo')['service_group_id']);
            echo json_encode($result);
        } else {
            action_log('User_RollBack', MODULE_NAME, session('UserInfo')['service_group_id']);
            $result['Msg'] = "参数错误！";
            echo json_encode($result);
        }
    }

    public function getVehicleMaintenanceInfoByVehicleId()
    {

        $result['Code'] = 200;
        $result['Msg'] = "获取失败";
        if (IS_POST) {
            $post_data = I('post.');
            $dao = D('vehicle_maintenance_info');//连接数据库
            $data = $dao->where(array(
                'vehicle_id' => $post_data['vehicleid'],
                'id' => $post_data['id']
            ))->find(); //查找语句
            if ($data) {
                $result['Result'] = $data;
                $result['Msg'] = "获取成功";
            }
        }
        echo json_encode($result);
    }

    public function exportData()
    {
        $result['Result'] = '';
        $result['Code'] = 200;
        $data = array(
            array('车牌号', '所属单位', '车辆保养时间', '保养原因', '保养类型', '保养费用', '保养情况', '备注'),
        );
        $dao_wordbook = D('word_book');
        $post_data = I('post.');
        if ($post_data) {
            foreach ($post_data['rows'] as $data_maintenance_info) {
                $content = array();
                array_push($content, $data_maintenance_info['vehicle_license']);
                array_push($content, $data_maintenance_info['group_name']);
                array_push($content, $data_maintenance_info['maintenance_time']);
                array_push($content, $data_maintenance_info['maintenance_reason']);
                $data_maintenance_info['maintenance_identify_state'] = $dao_wordbook->where(array(
                    'word_id' => $data_maintenance_info['maintenance_identify_state'],
                    'type_id' => 7
                ))->find()['word'];
                array_push($content, $data_maintenance_info['maintenance_identify_state']);
                array_push($content, $data_maintenance_info['maintenance_cost']);
                $data_maintenance_info['maintenance_state'] = $dao_wordbook->where(array(
                    'word_id' => $data_maintenance_info['maintenance_state'],
                    'type_id' => 6
                ))->find()['word'];
                array_push($content, $data_maintenance_info['maintenance_state']);
                array_push($content, $data_maintenance_info['maintenance_remark']);
                array_push($data, $content);
            }
        }
        $time = time();
        $fileName = "车辆保养记录~" . $time . ".xls";
        $result['Result'] = EXPORT_PATH . $fileName;

        create_xls($data, $fileName);


        echo json_encode($result);

    }
}