<?php

namespace Common\Controller;

use Think\Controller;

class CoreController extends Controller
{

    //全局用户信息
    public $UserInfo;

    /**
     * 后台控制器初始化
     */
    protected function _initialize()
    {

        if(is_login()){
            /*读取Admin/Config配置*/
            $config = C ( 'DB_CONFIG_DATA' );
            C ( $config );
            /*如果有用户登录，读取用户信息*/
            $this->UserInfo = session ( 'UserInfo' );
            $this->assign ( 'UserInfo', $this->UserInfo );
        }
	}

}