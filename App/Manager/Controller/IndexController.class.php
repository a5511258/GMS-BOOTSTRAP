<?php
namespace Manager\Controller;
use Think\Controller;
use Manager\Controller\ManagerCoreController;
//use Admin\Model\AuthRuleModel;
class IndexController extends ManagerCoreController {


    public function index(){
        $this->assign("UserInfo",$this->UserInfo);
        $rule = M ('auth_rule','gms_')->where(array('name'=>'Manager/Track/history'))->find();
        $this->assign('historyIcon',$rule['icon']);
        $rule = M ('auth_rule','gms_')->where(array('name'=>'Manager/RealVideo/videoForOne'))->find();
        $this->assign('realVideoIcon',$rule['icon']);
        $this->display();
    }

//    跟踪
    public function track($vehicle_id){
        $uid = session("Uid");
        if($uid){
            if(hasAuthForService('Manager/Index/track')){

                $this->assign("vehicle_id",$vehicle_id);
                $data = M('vehicle_info','service_')->where(array('vehicle_id'=>$vehicle_id))->find();
                $this->assign("data",json_encode($data));
                $this->assign("title","跟踪【".$data['vehicle_license']."】");
                $this->display();
            }else{
                $this->error("没有权限");
            }
        }else{
            $this->error ( "尚未登录", U ( C ( 'AUTH_USER_GATEWAY' ) ) );
        }
    }

    /**
     * 操作日志获取
     * 取最新的20条数据
     * 取当前用户的操作
     *
     */
    public function getOperateLog(){
        $uid = session("Uid");
        $dao = M("operate_log","manager_");

        $datas = $dao->where(array('user_id' => $uid))->order("time desc")->select();

        if($datas){
            $result['total'] = count($datas);
            $result['rows'] = $datas;
            echo json_encode($result);
        }else {
            $result['total'] = 0;
            $result['rows'] = array();
            echo json_encode($result);
        }
    }

    public function setOperateLog($vehicle_id,$operator_id = 0,$content="无操作"){
        $userInfo = session('UserInfo');
        $groupID = $userInfo['service_group_id'];
        $uid = $userInfo['id'];
        $data = array(
            'service_group_id'=>$groupID,
            'user_id'=>$uid,
            'time' => time(),
            'content' => $content,
            'operator_id'=>$operator_id,
            'vehicle_id'=>$vehicle_id,
        );

        $dao = M("operate_log","manager_");
        echo $dao->add($data);


    }


//    添加marker,弹出一个对话框，同时传递经纬度
    public function showAddMarker($windowId = "setting",$lat,$lng){
        $this->assign("windowId",$windowId);
        $this->assign("lat",$lat);
        $this->assign("lng",$lng);
        $this->display();
    }

    public function getMarkerType(){
        $arr = array();
        $arr[0]['id'] = 0 ;
        $arr[0]['text'] = "普通标注";

        $arr[1]['id'] = 1 ;
        $arr[1]['text'] = "线路点";
        echo json_encode($arr);
    }

//    查询车辆详细信息
    public function showCarInfosDetail($windowId = "setting",$vehicle_id){
        $this->assign("windowId",$windowId);
        $dao = M("vehicle_device_simcard_view","gms_");
        $data = $dao->where(array("vehicle_id"=>$vehicle_id))->find();
        $this->assign("data",$data);
//      TODO: 需要补充驾驶人信息，在html页面修改
        $driverDao = M("vehicle_driver_view","gms_");
        $drversData = $driverDao->where(array("vehicle_id"=>$vehicle_id))->select();

        $drverinfos = "";
        for($i = 0;$i < count($drversData);$i++){
            $info = $drversData[$i];
            $drverinfos .= '<div class="fitem">'.
                '<label>人员类型：</label>'.
                '<span >'.$info["word"].'</span>'.
                '<label>姓名：</label>'.
                '<span >'.$info["driver_name"].'</span>'.
                '<label>联系方式：</label>'.
                '<span >'.$info["tel_no"].'</span>'.
                ' </div>';
        }
        $this->assign("drverinfos",$drverinfos);
        $this->display();
    }

//    显示通用设置
    public function showCommonSetting($windowId = "setting",$vehicle_id){
        $this->assign("windowId",$windowId);
        $this->display();
    }
//  弃用
    public function showRealVideo($windowId = "setting",$vehicle_id){
        $this->assign("windowId",$windowId);
        $this->assign("vehicle_id",$vehicle_id);
        $this->display('RealVideo/index');
    }

//    展示区域选中数据
    public function showSelectedDatas($windowId = "setting"){
        $this->assign("windowId",$windowId);
        $this->display();
    }
//
    public function showFenceSendWin($windowId = "setting"){
        $this->assign("windowId",$windowId);
        $this->display();
    }



    /**
     *  添加标注到数据库
     * @param $title    名称
     * @param $type     类别
     * @param $comment  备注
     * @param $lat      纬度
     * @param $lng      经度
     */
    public function addMarker($title,$type,$comment,$lat,$lng){

        $arr = array();
        $arr[Code] = 200;

        echo json_encode($arr);
//        存储到数据库，并返回成功与否
    }


    public function latestHistoryInfos($vehicleIds){
        $result = array();
        for($i = 0; $i < count($vehicleIds);$i++){
//            数据库查询出结果，
//            返回一个json对象
            $id = $vehicleIds[$i];
            $dao = M("latest_gps",null,"DB_CONFIG1");
            $data = $dao->where(array("vehicle_id"=>$id))->find();
            if($data){
                $result[$id] = $data;
            }
        }
        echo json_encode($result);
    }

    public function getAlarmInfoById($id){
        $dao = M('alarm_record_view',null,'DB_CONFIG1');
        $data = $dao->where(array('id'=>$id))->find();
        if($data){
            echo json_encode($data);
        }else{
            echo json_encode("");
        }
    }
    public function editAlarmInfo(){
        $arr = array();
        $arr[Code] = 400;

        if(IS_POST){
            $postDatas = I("POST.");
            $data = array();
            $data['handlePeople'] = $postDatas['handlepeople'];
            $data['handleTime'] = $postDatas['handletime'];
            $data['handleMethod'] = $postDatas['handlemethod'];
            $data['handleDesc'] = $postDatas['handledesc'];
            $data['handleState'] = 1;
            $data['handlePeople_id'] = $postDatas['handlepeople_id'];
//            dump($data);

            $dao = M('alarm_record',null,'DB_CONFIG1');
//            dump($data);
            if($dao->where(array('id'=> $postDatas['id']))->save($data)){
                $arr[Code] = 200;
                echo json_encode($arr);
            }else {
                echo $dao->getError();

            }
        }
    }


    public function setting(){
        if(IS_POST){
            $dao = M("setting","manager_");
            $postDatas = I("POST.");
            if($dao->where(array('id'=> 0))->save($postDatas)){
                $arr[Code] = 200;
                echo json_encode($arr);
            }else {
                echo $dao->getError();
            }
        }else {
            $dao = M("setting","manager_");
            $data = $dao->find();
            $this->assign('online', $data['online']);
            $this->assign('offline', $data['offline']);
            $this->assign('online_stop', $data['online_stop']);
            $this->assign('time', $data['refresh_time']);
            $this->display();
        }
    }

    public function getImageSettingList(){
        $dao = M("car_image_setting","manager_");
        $datas = $dao->select();

        if($datas){
            echo json_encode($datas);
        }else {
            echo json_encode('');
        }
    }

    public function getCarImageUrl($key='online',$direction=0){
        $dao = M("setting","manager_");
        $dataTemp = $dao->find();
        dump($dataTemp);
        $Id = $dataTemp[$key];
        $dao = M("car_image_setting","manager_");
        $data = $dao->where(array('id'=>$Id))->find();
        $url = $data['path'].$direction.$data['extensions'];
//        echo '<img src="'.$url.'">';

        Header("Location: ".$url);

//        switch($type){
//            'image/jpeg':
//                header("Content-type:image/jpeg");
//                $img=imagecreatefromjpeg($img_file);
//                imagejpeg($img);
//                imagedestroy($img);
//            break;
//        'image/png':
//        header("Content-type:image/png");
//        $img=imagecreatefrompng($img_file);
//        imagepng($img);
//        imagedestroy($img);break;
//        'image/gif':
//        header("Content-type:image/gif");
//        $img=imagecreatefromgif($img_file);
//        imagegif($img);
//        imagedestroy($img);
//        }

    }

    public function selectPoint(){
        $this->display();
    }


}