<?php
/**
 *
 * 用于与终端通信，弹出对话框,对车辆右键，显示的内容
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/8/1
 * Time: 下午3:17
 */

namespace Manager\Controller;
use Think\Controller;
use DateTime;
use Service\Utils\JsonUtils;
class ConnectionController extends Controller {



    //全局用户信息
    public $UserInfo;

    /**
     * 后台控制器初始化
     */
    protected function _initialize() {

//        dump(session());


        /*如果有用户登录，读取用户信息*/
        if (session ( C ( 'AUTH_KEY' ) ) > 0) {
//            Log::record("执行了获取session",Log::ERR);
//            Log::write("执行了获取session",Log::ERR);

            $this->UserInfo = session ( 'UserInfo' );

            $this->assign ( 'UserInfo', $this->UserInfo );
        }else {

        }

    }

    public function index(){
        $this->display();
    }

//    显示文本下方窗口

    /**
     * 显示文本下方窗口
     * @param string $windowId
     */
    public function showSendTextView($windowId = "setting"){
        $this->assign("windowId",$windowId);
        $this->display();
    }

    /**
     * 显示拍照参数
     * @param string $windowId
     * @param $vehicle_id
     */
    public function showTakePhotoWin($windowId = "setting",$vehicle_id){
        $this->assign("windowId",$windowId);
        $this->assign("vehicle_id",$vehicle_id);
        $this->display();
    }
    /**
     * 显示电子围栏参数
     * @param string $windowId
     * @param $vehicle_id
     */
    public function showSendFenceWin($windowId = "setting",$vehicle_id){
        $this->assign("windowId",$windowId);
        $this->assign("vehicle_id",$vehicle_id);
        $this->display();
    }

    public function showAddFenceWinForLine($windowId = "setting"){
        $this->assign("windowId",$windowId);
        $this->display();
    }
    public function showAddFenceWinForRect($windowId = "setting"){
        $this->assign("windowId",$windowId);
        $this->display();
    }
    public function showAddFenceWinForRing($windowId = "setting"){
        $this->assign("windowId",$windowId);
        $this->display();
    }

    public function addFenceForRect(){

        $result = array();
        $result['Code'] = 400;
        $result['Msg'] = '添加失败';

        if (IS_POST) {
            $postDatas = I('POST.');
            $uid = $this->UserInfo['id'];

            if($uid){
                $time = new DateTime('now');
                $postDatas['create_time'] = $time->format('Y-m-d H:i:s');;
                $postDatas['create_user_id'] = $uid;
                $postDatas['datas'] = (new JsonUtils())->formatBeforeDecode($postDatas['datas']);
                $dao = M('fence_rect','manager_');
                if($dao->add($postDatas)){
                    $result['Code'] = 200;
                }else{
                    $result['Msg'] = '添加失败，请稍后再试！';
                }
            }
            echo json_encode($result);

        }
    }
    public function addFenceForLine(){

        $result = array();
        $result['Code'] = 400;
        $result['Msg'] = '添加失败';

        if (IS_POST) {
            $postDatas = I('POST.');
            $uid = $this->UserInfo['id'];
            if($uid){
                $time = new DateTime('now');
                $postDatas['create_time'] = $time->format('Y-m-d H:i:s');;
                $postDatas['create_user_id'] = $uid;
                $postDatas['datas'] = (new JsonUtils())->formatBeforeDecode($postDatas['datas']);
                $dao = M('fence_line','manager_');
                if($dao->add($postDatas)){
                    $result['Code'] = 200;
                }else{
                    $result['Msg'] = '添加失败，请稍后再试！';
                }
            }
            echo json_encode($result);

        }
    }
    public function addFenceForRing(){
        $result = array();
        $result['Code'] = 400;
        $result['Msg'] = '添加失败';

        if (IS_POST) {
            $postDatas = I('POST.');
            $uid = $this->UserInfo['id'];
            dump($postDatas);
            if($uid){
                $time = new DateTime('now');
                $postDatas['create_time'] = $time->format('Y-m-d H:i:s');;
                $postDatas['create_user_id'] = $uid;
                $postDatas['datas'] = (new JsonUtils())->formatBeforeDecode($postDatas['datas']);
                $dao = M('fence_ring','manager_');
                if($dao->add($postDatas)){
                    $result['Code'] = 200;
                }else{
                    $result['Msg'] = '添加失败，请稍后再试！';
                }
            }
            echo json_encode($result);

        }
    }

    public function getFenceDataByType($type){
        $result = array();
        $result['Code'] = 400;
        $result['Msg'] = '添加失败';

        $dao = M('fence_'.$type,'manager_');
        $datas = $dao->select();
        if($datas){
            $result['Code'] = 200;
            $result['Datas'] = $datas;
        }else{
            $result['Msg'] = '添加失败，请稍后再试！';
        }
        echo json_encode($result);

    }

}