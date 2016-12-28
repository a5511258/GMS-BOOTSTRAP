<?php
/**
 * Created by PhpStorm.
 * User: 李
 * Date: 2016/9/24 0024
 * Time: 16:19
 */

namespace Service\Controller;

use Service\Model\GroupInfoModel;
use Service\Model\VehicleInsuranceViewModel;
use Think\Controller;
use Service\Utils\GroupInfoUtils;
use Think\Log;

class VehicleInsuranceInfoController extends BaseSearchController
{

    function index()
    {
        $Utils = new GroupInfoUtils();
        $result = $Utils->Is_AuthUtils();
        $this->assign('edit', $result['edit']);
        $this->assign('del', $result['del']);
        $this->display();
    }

    public function getInsuranceInfoByVehicleId()
    {
        $result['Code'] = 200;
        $result['Msg'] = "获取失败";
        if (IS_POST) {
            $post_data = I('post.');
            $dao = D('vehicle_insurance_info');//连接数据库
            $data = $dao->where(array(
                'vehicle_id' => $post_data['vehicleid'],
                'policy_number' => $post_data['policy_number']
            ))->find(); //查找语句
            if ($data) {
                $result['Result'] = $data;
                $result['Msg'] = "获取成功";
            }
        }
        echo json_encode($result);
    }

    /**
     * 判断保险编号是否已存在
     * @param $Value        值
     */
    public function getExistPolicyNumber($Value)
    {
        $result = array();
        $result['Result'] = false;
        $result['Code'] = 200;
        $dao = D('vehicle_insurance_info');
        $n = $dao->where(array(
            'policy_number' => $Value
        ))->count();
        if ($n > 0) {
            $result['Result'] = true;
        }
        echo json_encode($result);

    }

    public function Create()
    {
        //数据未校验
        $post_data = I('post.');

        $dao = D('vehicle_insurance_info');

        $n = $dao->where(array(
            'insurance_name' => $post_data['insurance_name'],
            'policy_number' => $post_data['policy_number']
        ))->find();
        if ($n > 0) {

            returnErrorAndDie('相同保险公司下,保险编号不能重复');
        } else {

            $post_data['insurance_start_date'] = $post_data['insurance_start_date'] . " 00:00:00";

            $post_data['insurance_end_date'] = $post_data['insurance_end_date'] . " 23:59:59";

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

    }


    public function Update()
    {
        $post_data = I('post.');

        $dao = D('vehicle_insurance_info');
        $dao->startTrans();

        $id = $post_data['id'];

        $post_data['insurance_start_date'] = $post_data['insurance_start_date'] . " 00:00:00";

        $post_data['insurance_end_date'] = $post_data['insurance_end_date'] . " 23:59:59";

        unset($post_data['id']);

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
            $postdatas = I('post.');


            $dao = D('vehicle_insurance_info');//连接数据库


            if ($n = $dao->where(array(
                'vehicle_id' => $postdatas['VehicleId'],
                'policy_number' => $postdatas['PolicyNumber']))->delete()
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


//    public function formatData($returnData){
//
//        $returnData = parent::formatData($returnData);
//
//
//
//
////            dump($returnData);
//
//
//            foreach ($returnData as $k => $insurance){
//                $startTime = strtotime($insurance['insurance_start_date']);
//                $endTime = strtotime($insurance['insurance_end_date']);
//                $last_month = strtotime("-1 month",$endTime);
//                $nowTime = time();
//                if($startTime <= $nowTime && $nowTime <= $endTime){
//
//                    if($last_month<= $nowTime && $nowTime <= $endTime){
//                        $returnData[$k]['insurance_states'] = "近期到期";
//                    }
//                    else{
//                        $returnData[$k]['insurance_states'] = "未过期";
//                    }
//                }
//                else{
//                    $returnData[$k]['insurance_states'] = "已过期";
//                }
////                if($type == "1"){
////                    if($insurance['insurance_states'] == "未过期"){
////                        array_push($rows,$insurance);
////                    }
////                }
////                else if($type == "2"){
////                    if($insurance['insurance_states'] == "已过期"){
////                        array_push($rows,$insurance);
////                    }
////                }
////                else{
////                    array_push($rows,$insurance);
////                }
//            }
//
//            dump($returnData);
//
//        return $returnData;
//
//    }


    protected function _search()
    {
        $map = array();
        $post_data = I('post.');
        $Utils = new GroupInfoUtils();
        /* 名称：用户名 字段：username 类型：string*/
        if (isset($post_data['group_id']) && $post_data['group_id'] != '') {

            $group_ids = $Utils->getAllGroupID($post_data['group_id']);

            $map['group_id'] = array('in', $group_ids);

        } else {
            $group_id = session('UserInfo')['service_group_id'];

            $group_ids = $Utils->getAllGroupID($group_id);

            $map['group_id'] = array('in', $group_ids);

        }
        if (isset($post_data['vehicle_license']) && $post_data['vehicle_license'] != '') {
            $map["vehicle_license"] = array('like', '%' . $post_data['vehicle_license'] . '%');
        }

        if (isset($post_data['policy_number']) && $post_data['policy_number'] != '') {
            $map["policy_number"] = $post_data['policy_number'];
        }

        if (isset($post_data['insurance_name']) && $post_data['insurance_name'] != '') {
            $map["insurance_name"] = array('like', '%' . $post_data['insurance_name'] . '%');
        }

        if (isset($post_data['startTime']) && $post_data['startTime'] != '' && isset($post_data['endTime']) && $post_data['endTime'] != '') {
//            $map['insurance_start_date']= array('between', array($post_data['startTime']." 00:00:00",$post_data['endTime']." 23:59:59"));
            $map['insurance_start_date'] = array('EGT', $post_data['startTime'] . " 00:00:00");
//            $map['insurance_end_date']= array('between', array($post_data['startTime']." 00:00:00",$post_data['endTime']." 23:59:59"));
//            $map['insurance_end_date'] = array('EGT',$post_data['endTime']." 23:59:59");
        }

        $map_group['_logic'] = 'and';


        return $map;
    }

    public function getInsuranceToList()
    {

        $dao = D('vehicle_insurance_view');//连接数据库

        if (IS_POST) {
            $post_data = I('post.');
            $post_data ['first'] = $post_data ['rows'] * ($post_data ['page'] - 1);

            if (!isset($post_data['type']) || $post_data['type'] == '') {
                $post_data['type'] = "3";
            }


            $map = $this->_search();
            $res = array();

            $total = $dao->where($map)->count();
            if ($total == 0) {
                $res = array();
            } else {


                $_list = $dao->where($map)
                    ->order($post_data ['sort'] . ' ' . $post_data ['order'])
                    ->limit($post_data ['first'] . ',' . $post_data ['rows'])
                    ->select();
                foreach ($_list as $k => $insurance) {
//                    $startTime = strtotime($insurance['insurance_start_date']);
                    $endTime = strtotime($insurance['insurance_end_date']);
                    if ($post_data['type'] == "1") {
                        if ($_list[$k]['insurance_states'] == "未过期") {
                            array_push($res, $_list[$k]);
                        }
                    }

                    if (isset($post_data['limit']) && $post_data['limit'] != '') {

                        if ($post_data['limit'] == "1") {
                            $last_month = strtotime("-1 month", $endTime);
                        } else if ($post_data['limit'] == "7") {
                            $last_month = strtotime("-7 day", $endTime);
                        } else {
                            $last_month = strtotime("-3 month", $endTime);
                        }

                    } else {
                        $last_month = strtotime("-1 month", $endTime);
                    }


//                    $last_month = strtotime("-1 month",$endTime);
                    $nowTime = time();
                    if ($nowTime <= $endTime) {
                        if ($last_month <= $nowTime) {
                            $_list[$k]['insurance_states'] = "近期到期";
                        } else {
                            $_list[$k]['insurance_states'] = "未过期";
                        }
                    } else {
                        $_list[$k]['insurance_states'] = "已过期";
                    }


                    if ($post_data['type'] == "1") {
                        if ($_list[$k]['insurance_states'] == "未过期" || $_list[$k]['insurance_states'] == "近期到期") {
                            array_push($res, $_list[$k]);
                        }
                    } else if ($post_data['type'] == "2") {
                        if ($_list[$k]['insurance_states'] == "已过期") {
                            array_push($res, $_list[$k]);
                        }
                    } else if ($post_data['type'] == "4") {
                        if ($_list[$k]['insurance_states'] == "近期到期") {
                            array_push($res, $_list[$k]);
                        }
                    } else {
                        array_push($res, $_list[$k]);
                    }
                }
            }

            $total = count($res);
            $data = array(
                'total' => $total,
                'rows' => $res
            );
            echo json_encode($data);
        }


    }

    public function exportData()
    {
        $result['Result'] = "";
        $result['Code'] = 200;
        $data1 = array(
            array('车牌号', '所属单位', '保险公司名称', '保险类型', '保单号',
                '保险开始时间', '保险结束时间', '保单金额', '保单状态', '保单备注'),
        );
        $post_data = I('post.');
        if ($post_data) {
            foreach ($post_data['rows'] as $data_insurance_info) {
                $content = array();
                array_push($content, $data_insurance_info['vehicle_license']);
                array_push($content, $data_insurance_info['group_name']);
                array_push($content, $data_insurance_info['insurance_name']);

                if ($data_insurance_info['insurance_type'] == "1") {
                    array_push($content, "交强险");
                } else {
                    array_push($content, "商业险");
                }
                array_push($content, $data_insurance_info['policy_number']);
                array_push($content, $data_insurance_info['insurance_start_date']);
                array_push($content, $data_insurance_info['insurance_end_date']);
                array_push($content, $data_insurance_info['insurance_amount']);
                array_push($content, $data_insurance_info['insurance_states']);
                array_push($content, $data_insurance_info['insurance_remark']);
                array_push($data1, $content);
            }
        }
        $time = time();
        $fileName = "车辆保险记录导出~" . $time . ".xls";
        $result['Result'] = EXPORT_PATH . $fileName;
        echo json_encode($result);
        create_xls($data1, $fileName);
    }


}