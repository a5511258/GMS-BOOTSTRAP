<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16/11/11
 * Time: 上午9:02
 */

namespace Service\Controller;

use Service\Utils\GroupInfoUtils;

class BaseInfoController extends ServiceCoreController
{

    function index()
    {
        $Utils = new GroupInfoUtils();
        $result = $Utils->Is_AuthUtils();
        $this->assign('edit', $result['edit']);
        $this->assign('del', $result['del']);
        $this->display();
    }




}