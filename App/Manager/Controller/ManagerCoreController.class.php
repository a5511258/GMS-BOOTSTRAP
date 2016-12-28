<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/8/26
 * Time: 上午10:19
 */
namespace Manager\Controller;
use Common\Controller\CoreController;

class ManagerCoreController extends CoreController
{

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

            $dao = M("setting","manager_");
            $data = $dao->find();
            $this->assign('Setting',$data);

            $dao = M('group_info','service_');
            $data1 = $dao->where(array('group_id'=>$this->UserInfo['service_group_id']))->find();
            $this->assign('latlng',$data1['latlng']);
//            进一步判断授权
        }else {
            redirect(U(C('AUTH_USER_GATEWAY')));
        }

    }
}