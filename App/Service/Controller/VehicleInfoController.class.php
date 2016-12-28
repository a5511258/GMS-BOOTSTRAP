<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/7/13
 * Time: 下午4:49
 */

namespace Service\Controller;


use Think\Controller;
use Service\Utils\GroupInfoUtils;
use Think\Log;

class VehicleInfoController extends ServiceCoreController
{
    function index()
    {
        $Utils = new GroupInfoUtils();
        $result = $Utils->Is_AuthUtils();
        $this->assign('edit', $result['edit']);
        $this->assign('del', $result['del']);
        $this->display();
    }

    function changeGroup()
    {
        $this->display();
    }


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
            $map['vehicle_license'] = array('like', '%' . $post_data['vehicle_license'] . '%');
        }
        if (isset($post_data['vehicle_driver']) && $post_data['vehicle_driver'] != '') {
            $map['vehicle_driver'] = array('like', '%' . $post_data['vehicle_driver'] . '%');
        }
        if (isset($post_data['vehicle_brand']) && $post_data['vehicle_brand'] != '') {
            $map['vehicle_brand'] = array('like', '%' . $post_data['vehicle_brand'] . '%');
        }

        $map_group['_logic'] = 'and';


        return $map;
    }


//页面载入时显示车辆信息列表以及搜索
    public function getVehicleInfoToList()
    {
        $dao = D('vehicle_info');//连接数据库

        if (IS_POST) {
            $post_data = I('post.');
            $post_data ['first'] = $post_data ['rows'] * ($post_data ['page'] - 1);
//			$map = array ();
            $map = $this->_search();


            $total = $dao->where($map)->count();
            if ($total == 0) {
                $_list = array();
            } else {
                $_list = $dao->where($map)->relation(true)
                    ->order($post_data ['sort'] . ' ' . $post_data ['order'])
                    ->limit($post_data ['first'] . ',' . $post_data ['rows'])
                    ->select();
            }
            $data = array(
                'total' => $total,
                'rows' => $_list
            );
            echo json_encode($data);
        }


    }

    /**
     * 判断车牌号码是否已存在
     * @param $Value        值
     */
    public function getExistCarLicense($Value)
    {
        $result = array();
        $result['Result'] = false;
        $result['Code'] = 200;
        $dao = D('vehicle_info');
        $n = $dao->where(array(
            'vehicle_license' => $Value
        ))->count();
        if ($n > 0) {
            $result['Result'] = true;
        }
        echo json_encode($result);

    }//验证车牌是否唯一

    public function getExistDeviceName($Value)
    {
        $result = array();
        $result['Result'] = false;
        $result['Code'] = 200;
        $dao = D('vehicle_info');
        $n = $dao->where(array(
            'device_name' => $Value
        ))->count();
        if ($n > 0) {
            $result['Result'] = true;
        }
        echo json_encode($result);
    }//验证设备名称是否唯一

    public function getDeviceName()
    {
        $dao_vehicle = D('vehicle_info');
        $dao_devicename = D('keda_info');
        $map['device_name'] = array('NEQ', '');
        $data_used = $dao_vehicle->where($map)->field('device_name')->select();
        $used = array();
        foreach ($data_used as $item) {
            array_push($used, $item['device_name']);
        }
        //dump($used);
        $data = $dao_devicename->field('device_name')->select();
        $unused = array();
        foreach ($data as $item) {
            array_push($unused, $item['device_name']);
        }
        $unused = array_diff($unused, $used);
        $result = array();
        foreach ($unused as $k => $v) {
            //$temp['id'] = $k;
            $temp['word'] = $v;
            array_push($result, $temp);
        }
        echo json_encode($result);
    }

    //转组
    public function UpdateGroupId()
    {
        $result = array();
        $result['Code'] = 200;
        $result['Msg'] = "更新失败！";
        $result['Result'] = false;
        if (IS_POST) {
            $post_data = I('post.');
            $id = explode(",", $post_data['vehicle_id']);
            $dao_vehicle = D('vehicle_info');
            for ($i = 0; $i < count($id); $i++) {
                $dao_vehicle->startTrans();
                $res_vehicle = $dao_vehicle->where(array(
                    'vehicle_id' => $id[$i]
                ))->setField('group_id', $post_data['group_id']);
                //所有事物全部成功
                if (false !== $res_vehicle) {
                    $dao_vehicle->commit();//成功则提交
                    $result['Msg'] = "转组成功!";
                    $result['Result'] = true;
                } else {
                    $dao_vehicle->rollback();//不成功，则回滚
                }
            }
        }
        action_log('User_Change_Group', MODULE_NAME, session('UserInfo')['service_group_id']);
        echo json_encode($result);
    }//修改所属组

    /**
     * 通过车辆ID获取车辆详情
     */

    public function getVehicleInfoByVehicleId()
    {
        $result['Code'] = 200;
        $result['Msg'] = "获取失败";
        if (IS_POST) {
            $post_data = I('post.');
            $dao = D('vehicle_info');//连接数据库
            $data = $dao->where(array(
                'vehicle_id' => $post_data['vehicleid']
            ))->find(); //查找语句
            if ($data) {
                $result['Result'] = $data;
                $result['Msg'] = "获取成功";
            }
        }
        echo json_encode($result);
    }


    /**
     * 图片上传文件名称处理
     * @return string   返回文件名称字符串
     */

    public function fileUpload()
    {
        $result = "";
        $fileInfo = $_FILES['vehicle_photo'];
        $typeArr = explode("/", $fileInfo['type']);
        if (strtolower($typeArr[0]) !== "image") {
            return $result;
        }
        $suffix = "." . $typeArr[1];
        $fileName = md5_file($fileInfo["tmp_name"]) . $suffix;
        $path = UPLOAD_PATH . $fileName;
        $states = true;
        if (!file_exists($path)) {
            $states = move_uploaded_file($fileInfo["tmp_name"], $path);
        }
        if ($states) {
            $result = $fileName;
        }
        return $result;
    }


    //添加车辆信息
    public function Create()
    {
        //数据未校验
        $post_data = I('post.');
        $dao = D('vehicle_info');
        //判断上传图片大小是否为0
        if (0 != intval($_FILES['vehicle_photo']['size'])) {
            $uploadResult = $this->fileUpload();
            if ($uploadResult) {
                $post_data['vehicle_photo'] = $uploadResult;
            } else {
                returnError("图片上传失败!");
            }
        } else {

            $post_data['vehicle_photo'] = "";
        }

        Log::record("photo_name:" . $post_data['vehicle_photo']);
        $n = 1;
        $n -= $dao->where(array(
            'vehicle_license' => $post_data['vehicle_license']
        ))->count();
        if ($n < 1) {
            returnErrorAndDie("车牌号码重复");
        } else {
            unset($post_data['vehicle_id']);
            $dao->startTrans();


            $res = $dao->add($post_data);
            if (false !== $res) {

                $dao->commit();
                action_log('User_Add', MODULE_NAME, session('UserInfo')['service_group_id']);
                returnSuccess('操作成功!');

            } else {
                $dao->rollback();
                returnErrorAndDie("操作失败");

            }
        }

    }

    //编辑车辆信息
    //TODO: 需要添加设备校验

    public function Update()
    {
        $post_data = I('post.');
        $dao = D('vehicle_info');
        if (0 != intval($_FILES['vehicle_photo']['size'])) {
            $post_data['vehicle_photo'] = $this->fileUpload();
        } else {
            $post_data['vehicle_photo'] = $dao->where(array(
                'vehicle_id' => $post_data['vehicle_id']
            ))->field('vehicle_photo')->find()['vehicle_photo'];
        }
        Log::record("photo_name:" . $post_data['vehicle_photo']);
        $dao->startTrans();
        $res = $dao->where(array(
            'vehicle_id' => $post_data['vehicle_id']
        ))->save($post_data);
        if (false !== $res) {

            $dao->commit();
            returnSuccess('操作成功!');

            action_log('User_Edit', MODULE_NAME, session('UserInfo')['service_group_id']);
        } else {

            $dao->rollback();
            returnErrorAndDie("操作失败");
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


            $dao = D('vehicle_info');//连接数据库

            if ($n = $dao->where(array(
                'vehicle_id' => $postdatas['VehicleId'],
                'vehicle_license' => $postdatas['CarLicense']
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


    public function exportData()
    {
        $result['Result'] = '';
        $result['Code'] = 200;
        $dao_wordbook = D('word_book');
        $data = array(
            array('车牌号', '所属单位', '车辆类别', '车辆品牌', '车辆型号',
                '购买价格(元)', '购入日期', '驾驶员', '停放位置', '车辆说明'),
        );
        $post_data = I('post.');
        if ($post_data) {
            foreach ($post_data['rows'] as $data_vehicle_info) {
                $content = array();
                array_push($content, $data_vehicle_info['vehicle_license']);
                array_push($content, $data_vehicle_info['group_name']);
                $data_vehicle_info['vehicle_type'] = $dao_wordbook->where(array(
                    'word_id' => $data_vehicle_info['vehicle_type'],
                    'type_id' => 1
                ))->find()['word'];
                array_push($content, $data_vehicle_info['vehicle_type']);
                array_push($content, $data_vehicle_info['vehicle_brand']);
                array_push($content, $data_vehicle_info['vehicle_model']);
                array_push($content, $data_vehicle_info['vehicle_buy_price']);
                array_push($content, $data_vehicle_info['vehicle_buy_date']);
                array_push($content, $data_vehicle_info['vehicle_driver']);
                array_push($content, $data_vehicle_info['vehicle_place']);
                array_push($content, $data_vehicle_info['vehicle_explain']);
                array_push($data, $content);
            }
        }
        $time = time();
        $fileName = "车辆信息管理" . $time . ".xls";
        $result['Result'] = "/CourtGms/Export/" . $fileName;
        create_xls($data, $fileName);
        echo json_encode($result);
    }


}