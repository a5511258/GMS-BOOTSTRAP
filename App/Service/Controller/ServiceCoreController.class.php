<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16/8/26
 * Time: 上午10:19
 */
namespace Service\Controller;
use Common\Controller\CoreController;
class ServiceCoreController extends CoreController 
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
            $this->UserInfo = session ( 'UserInfo' );
            $this->assign ( 'UserInfo', $this->UserInfo );

        }else {

            header("Location: ".U ( C ( 'AUTH_USER_GATEWAY' ) ));
        }

    }
}