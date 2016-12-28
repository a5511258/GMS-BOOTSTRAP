<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16/9/22
 * Time: 下午4:08
 */

namespace Service\Controller;


use Service\Model\GroupInfoModel;
use Service\Model\VehicleViolationViewModel;
use Think\Controller;
use Service\Utils\GroupInfoUtils;

class VehicleViolationInfoController extends BaseSearchController
{
    function index()
    {
        $Utils = new GroupInfoUtils();
        $result = $Utils->Is_AuthUtils();
        $this->assign('edit', $result['edit']);
        $this->assign('del', $result['del']);
        $this->display();
    }


    public function getVehicleViolationInfoToList($startTime = "", $endTime = "", $vehicle_license = "")
    {
        $maintenanceViewModel = new VehicleViolationViewModel();
        $where = array();

        if ($startTime != '' && $endTime != '') {
            $where['violation_time'] = array('between',
                array($startTime . " 00:00:00", $endTime . " 23:59:59"));
        }
        if ($vehicle_license) {
            $where["vehicle_license"] = $vehicle_license;
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
        $result = array();
        $result['Result'] = false;
        $result['Code'] = 200;
        $dao = D('vehicle_violation_info');
        $dao->startTrans();
        $res = $dao->add($post_data);
        if (false !== $res) {
            $result['Result'] = true;
            $dao->commit();
        } else {
            $dao->rollback();
        }
        action_log('User_Add', MODULE_NAME, session('UserInfo')['service_group_id']);
        echo json_encode($result);
    }

    public function Update()
    {
        $post_data = I('post.');
        $dao = D('vehicle_violation_info');
        $id = $post_data['id'];
        unset($post_data['id']);
        $dao->startTrans();
        $res = $dao->where(array(
            'vehicle_id' => $post_data['vehicle_id'],
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
            $post_data = I('post.');

            $dao = D('vehicle_violation_info');//连接数据库

            if ($n = $dao->where(array(
                'vehicle_id' => $post_data['VehicleId'],
                'id' => $post_data['id']
            ))->delete()
            ) {
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

    public function getVehicleViolationInfoByVehicleId()
    {

        $result['Code'] = 200;
        $result['Msg'] = "获取失败";
        if (IS_POST) {
            $post_data = I('post.');
            $dao = D('vehicle_violation_info');//连接数据库
            $data = $dao->where(array(
                'vehicle_id' => $post_data['vehicleId'],
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
            array('车牌号', '所属单位', '驾驶员姓名', '违规事件', '违规扣分情况', '处罚金额', '违规位置', '违规时间'),
        );
        $post_data = I('post.');
        if ($post_data) {
            foreach ($post_data['rows'] as $data_violation_info) {
                $content = array();
                array_push($content, $data_violation_info['vehicle_license']);
                array_push($content, $data_violation_info['group_name']);
                array_push($content, $data_violation_info['vehicle_driver']);
                array_push($content, $data_violation_info['violation_name']);
                array_push($content, $data_violation_info['violation_points']);
                array_push($content, $data_violation_info['violation_cost']);
                array_push($content, $data_violation_info['violation_location']);
                array_push($content, $data_violation_info['violation_time']);
                array_push($content, $data_violation_info['violation_remark']);
                array_push($data, $content);
            }
        }
        $time = time();
        $fileName = "车辆规费记录导出~" . $time . ".xls";
        $result['Result'] = EXPORT_PATH . $fileName;
        echo json_encode($result);
        create_xls($data, $fileName);
    }
}