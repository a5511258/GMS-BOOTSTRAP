<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/11/11
 * Time: 上午8:58
 */

namespace Service\Controller;

use Service\Model\DeviceSimCardBindModel;
use Service\Model\WordBookModel;


class SimCardInfoController extends BaseInfoController
{

    //系统默认模型
    private $Model = null;

    protected function _initialize()
    {
        //继承初始化方法
        parent::_initialize();
        //设置控制器默认模型
        $this->Model = D('simcard_info');

    }

    /**
     *
     * 获取数据
     */
    public function getSimCardInfoToList()
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

            //获取绑定设备信息

            $Bind = new DeviceSimCardBindModel();

            $bindDevice = $Bind->getBindInfo($Bind::Type_DESC, $_info['Data']['id']);


            $_info['Data']['device_id'] = $bindDevice['id'];

            $_info['Data']['device_no'] = $bindDevice['device_no'];

            $_info = json_encode($_info['Data']);

            $this->assign('_info', $_info);

        }

        $this->assign('_type', $type);

        $temp = $wordBookModel->getMap($wordBookModel::TYPE_SIMCARD_NET_TYPE);

        $dataType = array();

        foreach ($temp as $key => $val) {

            $temp1['id'] = $key;
            $temp1['text'] = $val;

            array_push($dataType, $temp1);
        }

        $this->assign('_dataType', json_encode($dataType));

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

    public function getAllSimCard(){

        $res = $this->Model->getEffectiveInfo();

        echo json_encode($res);
    }



    public function getBindSimCard($type='bind',$groupID){

        $res = $this->Model->getEffectiveInfo($type,$groupID);

        echo json_encode($res);
    }


    public function changeGroup(){


        $res = $this->Model->ChangeGroup();

        echo json_encode($res);
    }



}