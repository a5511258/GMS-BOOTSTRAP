<?php
/**
 * Created by PhpStorm.
 * User: lgq
 * Date: 16/8/5
 * Time: 下午2:37
 */

function hasAuthForService($url){
    $Auth = new \Common\Libs\Auth();
    $AUTH_KEY=session(C('AUTH_KEY'));
//        echo "uid:";
//        dump($AUTH_KEY);
//        //当前权限表达式
//        $Auth_Rule = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
//        $a = 'Admin/Index/index';
//        echo "uid:";
//        dump($url);
    if (! $Auth->check ($url,$AUTH_KEY)) {
//            echo "ok";
        return false;
    }else{
        return true;
    }
}