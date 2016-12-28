<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/8/24
 * Time: 上午11:50
 */

namespace Manager\Controller;
use Think\Controller;
use Manager\Controller\ManagerCoreController;
class RealVideoController extends ManagerCoreController
{

    public function index(){
        $this->display();

    }
    public function videoForOne($vehicle_id,$channe_index){
        $dao = M('vehicle_detail_view','service_');
        $data = $dao->where(array('vehicle_id'=>$vehicle_id))->find();
        $this->assign('vehicle_license',$data['vehicle_license']);
        $this->assign('channe_index',$channe_index);
        $this->assign('device_id',$data['device_id']);
        $this->assign('domain_id',$data['domain_id']);
        $this->display();
    }
}