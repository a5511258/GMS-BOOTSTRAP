<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16/11/11
 * Time: 上午8:56
 */

namespace Service\Controller;

use Service\Model\WordBookModel;
use Service\Model\DeviceSimCardBindModel;


class DeviceInfoController extends BaseInfoController
{

    //系统默认模型
    private $Model = null;

    protected function _initialize()
    {
        //继承初始化方法
        parent::_initialize();
        //设置控制器默认模型
        $this->Model = D('device_info');

    }

    /**
     *
     * 获取数据
     */
    public function getDeviceInfoToList()
    {

        $res = $this->Model->IndexData();

        echo json_encode($res);
    }


    public function add_edit()
    {

        $wordBookModel = new WordBookModel();

        $type = I('get.type');

        if ('add' == $type) {
            $type = "添加";
        } else {
            $type = "编辑";

            $_info = $this->Model->EditData();


            $Bind = new DeviceSimCardBindModel();


            $bindDevice = $Bind->getBindInfo($Bind::Type_ASC, $_info['Data']['id']);


            $_info['Data']['simcard_id'] = $bindDevice['id'];

            $_info['Data']['simcard_no'] = $bindDevice['simcard_no'];

            $_info = json_encode($_info['Data']);


            $this->assign('_info', $_info);

        }

        $this->assign('_type', $type);

        $temp = $wordBookModel->getMap($wordBookModel::TYPE_DEVICE_TYPE);

        $dataType = array();

        foreach ($temp as $key => $val) {

            $temp1['id'] = $key;
            $temp1['text'] = $val;

            array_push($dataType, $temp1);
        }

        $this->assign('_dataType', json_encode($dataType));

        $temp = $wordBookModel->getMap($wordBookModel::TYPE_CHANNEL_NUM_TYPE);

        $dataChannel = array();

        foreach ($temp as $key => $val) {

            $temp1['id'] = $key;
            $temp1['text'] = $val;

            array_push($dataChannel, $temp1);
        }


        $this->assign('_dataChannel', json_encode($dataChannel));


        $this->display();
    }

    /**
     * 通过ID + group_id 删除某条数据
     */
    public function Delete()
    {
        $res = $this->Model->DeleteData();

        echo json_encode($res);
    }

    /**
     * 添加
     */
    public function Create()
    {
        $res = $this->Model->CreateData();

        echo json_encode($res);
    }

    /**
     * 编辑
     */
    public function Edit()
    {
        $res = $this->Model->EditData();

        echo json_encode($res);

    }

    public function exportData()
    {


        $res = $this->Model->ExportData();

        echo json_encode($res);
    }

    public function getAllDevice()
    {

        $res = $this->Model->getEffectiveInfo();

        echo json_encode($res);
    }


    public function getBindDevice($type = 'bind', $groupID)
    {

        $res = $this->Model->getEffectiveInfo($type, $groupID);

        echo json_encode($res);
    }


    public function transferGroup()
    {
        $res = $this->Model->ChangeGroup();

        echo json_encode($res);

    }
}