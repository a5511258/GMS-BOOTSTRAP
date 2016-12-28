<?php
/**
 * 轨迹回放
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/8/12
 * Time: 上午8:38
 */

namespace Manager\Controller;
use Think\Controller;
use Manager\Controller\ManagerCoreController;
class TrackController extends ManagerCoreController
{
    public function index($vehicle_id)
    {
        $uid = session("Uid");
        if($uid){
            if(hasAuthForService('Manager/Track/history')){
                $this->assign("vehicle_id",$vehicle_id);
                $data = M('vehicle_info','service_')->where(array('vehicle_id'=>$vehicle_id))->find();
                $this->assign("data",json_encode($data));
                $this->assign("title","路径回放【".$data['vehicle_license']."】");
                $this->assign("vehicle_license",$data['vehicle_license']);
                $this->display();
            }else{
                $this->error("没有权限");
            }
        }else{
            $this->error ( "尚未登录", U ( C ( 'AUTH_USER_GATEWAY' ) ) );
        }

    }


//    获取车队列表
    public function getCarGroupList(){
//        1.获取当前用户的group——id_level
        $groupId = $this->UserInfo['service_group_id'];

        $dao = M("group_info","service_");
        $id_level = $dao->where(array("group_id"=>$groupId))->field('id_level')->find();
        if($id_level){
            $map['id_level'] = array('LIKE',$id_level['id_level'] . '%');
            $map['group_type'] = 2;

            $datas = $dao->where($map)->field('group_id,group_name')->select();
            $result = array();
            $result[0]["id"] = -1;
            $result[0]["text"] = 全部;

//            dump($datas);
            for ($i = 1; $i<=count($datas);$i++){
                $result[$i]["id"] = $datas[$i-1]["group_id"];
                $result[$i]["text"] = $datas[$i-1]["group_name"];
            }
            echo json_encode($result);
        }
    }

    public function  getCarList($groupId=-1){
        $group_id = $groupId;
        if($group_id == -1){
            $group_id = $this->UserInfo['service_group_id'];
        }

        $dao = M("group_info","service_");
        $id_level = $dao->where(array("group_id"=>$group_id))->field('id_level')->find();
        if($id_level){
            $daoTemp = M("group_vehicle_view","gms_");
            $map['id_level'] = array('LIKE',$id_level['id_level'] . '%');
            $map['group_type'] = 2;

            $datas = $daoTemp->where($map)->field('vehicle_id,car_license')->select();
            $result = array();
            for ($i = 0; $i<count($datas);$i++){
                $result[$i]["id"] = $datas[$i]["vehicle_id"];
                $result[$i]["text"] = $datas[$i]["car_license"];
            }
            echo json_encode($result);

        }
    }

//    30000 条数据支持
    public function getTrackList()
    {
        $result = array();
        $result['Code'] = 400;
        $result['Datas'] = "";
        if (IS_POST) {
            $post_data=I('post.');
            $vehicle_id = $post_data['vehicle_id'];
            $start_time = $post_data['start_time'];
            $end_time = $post_data['end_time'];

            $dao = M("gps",null,"DB_CONFIG1");
            $map['vehicle_id'] = $vehicle_id;
            $map['time'] = array('between',$start_time.",".$end_time);
            $count = $dao->where($map)->count();

//            if($count>30000){
//                $result['Code'] = 400;
//                $result['Msg'] = "数据量太大，建议分段查询！";
//                echo json_encode($result);
//                return;
//            }

            $sql = "SELECT * FROM `gps` WHERE `vehicle_id` = ".$vehicle_id." AND `time` BETWEEN '".$start_time."' AND '".$end_time."';";

            $data = $dao->query($sql);
            if ($data){
                $result['Code'] = 200;
                $result['Count'] = $count;
                $result['Datas'] = $data;

            }

            echo json_encode($result);
        }
    }

    public function getAlertInfos(){
        $result = array();
        $result['Code'] = 400;
        $result['Datas'] = "";
        if (IS_POST) {
            $post_data=I('post.');
            $vehicle_id = $post_data['vehicle_id'];
            $start_time = $post_data['start_time'];
            $end_time = $post_data['end_time'];

            $dao = M("alarm_record_view",null,"DB_CONFIG1");

//            查找报警信息在开始与结束时间内的信息。
            $sql = "SELECT * FROM `alarm_record_view` WHERE `vehicle_id` = ".$vehicle_id." AND `start_time` BETWEEN '".$start_time."' AND '".$end_time."'AND `end_time` BETWEEN '".$start_time."' AND '".$end_time."'";
            $data = $dao->query($sql);
            if ($data){
                $result['Code'] = 200;
                $result['Datas'] = $data;
            }

            echo json_encode($result);
        }
    }

    public function history($vehicle_id){
        $uid = session("Uid");
        if($uid){
            if(hasAuthForService('Manager/Track/history')){
                $this->assign("vehicle_id",$vehicle_id);
                $data = M('vehicle_info','service_')->where(array('vehicle_id'=>$vehicle_id))->find();
                $this->assign("data",json_encode($data));
                $this->assign("title","历史【".$data['vehicle_license']."】");
                $this->assign("vehicle_license",$data['vehicle_license']);

//                获取点数据，返回显示


                $this->display();
            }else{
                $this->error("没有权限");
            }
        }else{
            $this->error ( "尚未登录", U ( C ( 'AUTH_USER_GATEWAY' ) ) );
        }
    }

}