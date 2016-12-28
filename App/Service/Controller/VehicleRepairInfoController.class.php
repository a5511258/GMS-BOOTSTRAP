<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16/9/22
 * Time: 下午4:08
 */

namespace Service\Controller;


use Think\Controller;
use Service\Utils\GroupInfoUtils;

class VehicleRepairInfoController extends ServiceCoreController
{
    function index()
    {
        $Utils = new GroupInfoUtils();
        $result = $Utils->Is_AuthUtils();
        $this->assign('edit', $result['edit']);
        $this->assign('del', $result['del']);
        $this->display();
    }

    protected function _search($post_data)
    {
        $map = array();
//        $post_data=I('post.');

        $Utils = new GroupInfoUtils();
        $group_id = session('UserInfo')['service_group_id'];
        $group_ids = $Utils->getAllGroupID($group_id);
        $map['group_id'] = array('in', $group_ids);
        $startTime = '';
        $endTime = '';
        if (isset($post_data['startTime']) && $post_data['startTime'] != '') {
            $startTime = $post_data['startTime'];
        }
        if (isset($post_data['endTime']) && $post_data['endTime'] != '') {
            $endTime = $post_data['endTime'];
        }
        if ($startTime != '' && $endTime != '') {
            $map['damage_time'] = array('between',
                array($startTime . " 00:00:00", $endTime . " 23:59:59"));
        }
        if (isset($post_data['vehicleid']) && $post_data['vehicleid'] != '') {
            $map['vehicle_id'] = $post_data['vehicleid'];
        }
        $map['_logic'] = 'and';
        return $map;
    }

    public function getVehicleRepairInfoToList()
    {

        $dao_repair = D('vehicle_repair_view');


        $post_data = I('post.');
        $post_data ['first'] = $post_data ['rows'] * ($post_data ['page'] - 1);

        $map = $this->_search($post_data);

        $total = $dao_repair->where($map)->count();


        if ($total == 0) {
            $_list = [];
        } else {
            $_list = $dao_repair->where($map)->order($post_data ['sort'] . ' ' . $post_data ['order'])->limit($post_data ['first'] . ',' . $post_data ['rows'])->select();

        }
        $data = array(
            'total' => $total,
            'rows' => $_list
        );
        echo json_encode($data);

    }

    public function Create()
    {
        //数据未校验
        $post_data = I('post.');
        $dao = D('vehicle_repair_info');
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

        $dao = D('vehicle_repair_info');

        $id = $post_data['id'];
        unset($post_data['vehicle_license']);
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
            $postdatas = I('post.');


            $dao = D('vehicle_repair_info');//连接数据库


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

    public function getVehicleRepairInfoByVehicleId()
    {

        $result['Code'] = 200;
        $result['Msg'] = "获取失败";
        if (IS_POST) {
            $post_data = I('post.');
            $dao = D('vehicle_repair_info');//连接数据库
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
            array('车牌号', '所属单位', '车辆损坏时间', '维修原因', '损坏程度', '维修费用', '维修情况', '备注'),
        );
        $dao_wordbook = D('word_book');
        $post_data = I('post.');
        if ($post_data) {
            foreach ($post_data['rows'] as $data_repair_info) {
                $content = array();
                array_push($content, $data_repair_info['vehicle_license']);
                array_push($content, $data_repair_info['group_name']);
                array_push($content, $data_repair_info['damage_time']);
                array_push($content, $data_repair_info['repair_reason']);
                $data_repair_info['repair_identify_state'] = $dao_wordbook->where(array(
                    'word_id' => $data_repair_info['repair_identify_state'],
                    'type_id' => 5
                ))->find()['word'];
                array_push($content, $data_repair_info['repair_identify_state']);
                array_push($content, $data_repair_info['repair_cost']);
                $data_repair_info['repair_state'] = $dao_wordbook->where(array(
                    'word_id' => $data_repair_info['repair_state'],
                    'type_id' => 4
                ))->find()['word'];
                array_push($content, $data_repair_info['repair_state']);
                array_push($content, $data_repair_info['repair_remark']);
                array_push($data, $content);
            }
        }
        $time = time();
        $fileName = "车辆维修记录导出~" . $time . ".xls";
        $result['Result'] = EXPORT_PATH . $fileName;

        create_xls($data, $fileName);

        echo json_encode($result);
    }
}