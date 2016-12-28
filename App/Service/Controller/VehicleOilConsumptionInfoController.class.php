<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/11/1
 * Time: 下午2:45
 */

namespace Service\Controller;

use Service\Utils\GroupInfoUtils;
use Service\Model\VehicleOilConsumptionViewModel;

class VehicleOilConsumptionInfoController extends ServiceCoreController
{

    //系统默认模型
    private $Model = null;

    protected function _initialize()
    {
        //继承初始化方法
        parent::_initialize();
        //设置控制器默认模型
        $this->Model = D('vehicle_oil_consumption_info');
    }

    function index()
    {
        $Utils = new GroupInfoUtils();
        $result = $Utils->Is_AuthUtils();
        $this->assign('edit', $result['edit']);
        $this->assign('del', $result['del']);
        $this->display();
    }

    protected function getIndexModel()
    {
        return new VehicleOilConsumptionViewModel();
    }

    public function getVehicleOilConsumptionInfoToList()
    {
        echo json_encode($this->getInfo());
    }

    public function Create()
    {
        //数据未校验
        $post_data = I('post.');

        $this->Model->startTrans();

        unset($post_data['id']);

        $data = $this->Model->create($post_data);

        $res = $this->Model->add($data);
        if (false !== $res) {
            $this->Model->commit();

            action_log('User_Add', MODULE_NAME, session('UserInfo')['service_group_id']);

            returnSuccess('操作成功!');
        } else {
            $this->Model->rollback();

            returnError('操作失败!');
        }


    }


    public function Update()
    {

        if (IS_POST) {
            $post_data = I('post.');

            $this->Model->startTrans();

            $id = $post_data['id'];

            $vehicle_id = $post_data['vehicle_id'];


            unset($post_data['id']);
            unset($post_data['vehicle_id']);
            $data = $this->Model->create($post_data);


            if ($data) {
                $res = $this->Model->where(array(
                    'vehicle_id' => $vehicle_id,
                    'id' => $id
                ))->save($data);
                if (false !== $res) {

                    $this->Model->commit();
                    action_log('User_Edit', MODULE_NAME, session('UserInfo')['service_group_id']);
                    returnSuccess('操作成功!');

                } else {

                    $this->Model->rollback();

                    returnError('操作失败!');
                }

            }


        } else {

            $Model = new VehicleOilConsumptionViewModel();

            echo $Model->getInfoByID();
        }

    }


    public function Delete()
    {
        $this->Model->del();
    }

    public function exportData()
    {

        $postData = I('post.');

        $Model = new VehicleOilConsumptionViewModel();

        echo $Model->export($postData);
    }


}