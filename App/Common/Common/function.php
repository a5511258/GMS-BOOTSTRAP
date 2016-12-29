<?php
const APP_ADDON_PATH = './App/Addons/';
/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login()
{
    $user = session(C('AUTH_KEY'));
    if (empty($user)) {
        return 0;
    } else {
        return session(C('AUTH_KEY'));
    }
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 */
function is_admin($uid = null)
{
    $uid = is_null($uid) ? is_login() : $uid;
    if (in_array($uid, C('AUTH_ADMIN'))) {
        return true;
    } else {
        return false;
    }
}

function Is_Auth($Auth_Rule)
{
    if ($Auth_Rule) {
        $Auth = new \Common\Libs\Auth();
        $AUTH_KEY = session(C('AUTH_KEY'));
        Think\Log::record('$Auth_Rule' . $Auth_Rule);
        $controller = explode('/', $Auth_Rule);

        //判定当前$controller数组中是否存在非认证模块
        if (!in_array($controller, explode(",", C("NOT_AUTH_MODULE")))) {
            //当前权限表达式
            //$Auth_Rule = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;

            if (!$Auth->check($Auth_Rule, $AUTH_KEY)) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }


}

function Is_Auth_Check($Auth_Rule)
{
    $Auth = new \Common\Libs\Auth();
    $AUTH_KEY = session(C('AUTH_KEY'));
    Think\Log::record('$Auth_Rule' . $Auth_Rule);
    //判断当前认证key是否不在 超级管理组配置中,或者当前模块是否为非认证模块
//	if (! is_admin($AUTH_KEY) && ! in_array ( CONTROLLER_NAME, explode ( ",", C ( "NOT_AUTH_MODULE" ) ) )) {
    $controller = explode('/', $Auth_Rule);


    if (!in_array($controller[0], explode(",", C("NOT_AUTH_MODULE")))) {
        //当前权限表达式
        //$Auth_Rule = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;

        if (!$Auth->check($Auth_Rule, $AUTH_KEY)) {
            return false;
        } else {
            return true;
        }
    } else {
        return true;
    }
}


/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
//function get_nickname($uid = 0){
//    static $list;
//    if(!($uid && is_numeric($uid))){ //获取当前登录用户名
//        return session('userinfo.nickname');
//    }
//    /* 获取缓存数据 */
//    if(empty($list)){
//        $list = S('sys_user_nickname_list');
//    }
//    /* 查找用户信息 */
//    $key = "u{$uid}";
//    if(isset($list[$key])){ //已缓存，直接使用
//        $name = $list[$key];
//    } else { //调用接口获取用户信息
//        $info = M('User')->field('nickname')->find($uid);
//        if($info !== false && $info['nickname'] ){
//            $nickname = $info['nickname'];
//            $name = $list[$key] = $nickname;
//            /* 缓存用户 */
//            $count = count($list);
//            $max   = C('USER_MAX_CACHE');
//            while ($count-- > $max) {
//                array_shift($list);
//            }
//            S('sys_user_nickname_list', $list);
//        } else {
//            $name = '';
//        }
//    }
//    return $name;
//}
/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户名称
 */
function get_username($uid = 0)
{
    static $list;
    if (!($uid && is_numeric($uid))) { //获取当前登录用户名
        return session('userinfo.username');
    }
    /* 获取缓存数据 */
    if (empty($list)) {
        $list = S('sys_user_username_list');
    }
    /* 查找用户信息 */
    $key = "u{$uid}";
    if (isset($list[$key])) { //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $info = M('User')->field('username')->find($uid);
        if ($info !== false && $info['username']) {
            $username = $info['username'];
            $name = $list[$key] = $username;
            /* 缓存用户 */
            $count = count($list);
            $max = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_user_username_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}

/**
 * 根据控制器名称获取模块名称
 * @param  string $controller 控制器名称
 * @return string              模块名称
 */
function get_controllername($controller)
{

    $name = "";
    if (CONTROLLER_NAME == "GroupInfo") {
        $name = "组织管理模块";
    }
    if (CONTROLLER_NAME == "DeviceInfo") {
        $name = "设备管理模块";
    }
    if (CONTROLLER_NAME == "PeopleInfo") {
        $name = "人员管理模块";
    }
    if (CONTROLLER_NAME == "SimCardInfo") {
        $name = "SIM卡管理模块";
    }
    if (CONTROLLER_NAME == "VehicleInfo") {
        $name = "车辆管理模块";
    }
    if (CONTROLLER_NAME == "VehicleRepairInfo") {
        $name = "维修管理模块";
    }
    if (CONTROLLER_NAME == "VehicleMaintenanceInfo") {
        $name = "保养管理模块";
    }
    if (CONTROLLER_NAME == "VehicleViolationInfo") {
        $name = "车辆管理模块";
    }
    return $name;
}

/**
 * 根据时间戳返回时间
 * @param  Time $time 时间戳
 * @return string      时间值
 */
function time_format($time)
{
    $time = date('Y-m-d H:i:s', $time);
    return $time;
}


/**
 * 根据模块的标识和版本判断是否可以安装模块
 * @param  string $name 模块名称
 * @param  string $module_version 模块版本
 * @return integer       模块比较结果
 * 1 不存在次模块
 * 2 当前版本低于需求版本
 * 3 需求模块未启用
 * 9 模块比较正常
 */
function validate_module($name, $module_version)
{
    //设置模块路径
    $path = APP_PATH . $name;
    //判断是否存在相应模块的配置文件
    if (file_exists($path . DIRECTORY_SEPARATOR . 'Install' . DIRECTORY_SEPARATOR . 'Config.inc.php')) {
        //读取配置文件
        @include($path . DIRECTORY_SEPARATOR . 'Install' . DIRECTORY_SEPARATOR . 'Config.inc.php');
        //判断模块的已安装模块的版本和当前需求版本的比较，如果已安装模块的版本低于当前需求模块的版本
        if (!version_compare($version, $module_version, '<')) {
            return 2;
        } else {
            $module_info = M('Module')->where(array('module' => $name))->find();
            if ($module_info['version']) {
                return 9;
            } else {
                return 3;
            }
        }
    } else {
        return 1;
    }
}


function del_AuthRule($AuthRule_arr)
{
    foreach ($AuthRule_arr as $AuthRule) {
        M("AuthRule")->where(array("name" => $AuthRule))->delete();
    }
}

function check_verify($code, $id = 1)
{
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {

                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map 映射关系二维数组  array(
 *                                          '字段名1'=>array(映射关系数组),
 *                                          '字段名2'=>array(映射关系数组),
 *                                           ......
 *                                       )
 * @author 朱亚杰 <zhuyajie@topthink.net>
 * @return array
 *
 *  array(
 *      array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *      ....
 *  )
 *
 */
function int_to_string(&$data, $map = array('status' => array(1 => '正常', -1 => '删除', 0 => '禁用', 2 => '未审核', 3 => '草稿')))
{
    if ($data === false || $data === null) {
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row) {
        foreach ($map as $col => $pair) {
            if (isset($row[$col]) && isset($pair[$row[$col]])) {
                $data[$key][$col . '_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc':// 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str 要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str, $glue = ',')
{
    return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array $arr 要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr, $glue = ',')
{
    return implode($glue, $arr);
}

/**
 * 获取行为数据
 * @param string $id 行为id
 * @param string $field 需要获取的字段
 */
function get_action($id = null, $field = null)
{
    if (empty($id) && !is_numeric($id)) {
        return false;
    }
    $list = S('action_list');
    if (empty($list[$id])) {
        D('Action')->cache();
        $list = S('action_list');
    }
    return empty($field) ? $list[$id]['title'] : $list[$id][$field];
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null, $log_source = 2)
{

    //参数检查
    if (empty($action) || empty($model) || empty($record_id)) {
        return '参数不能为空';
    }
    if (empty($user_id)) {
        $user_id = is_login();
    }

    //查询行为,判断是否执行
    $action_info = M('Action')->where(array('name' => $action))->find();
    if ($action_info['status'] != 1) {
        return '该行为被禁用或删除';
    }

    //插入行为日志
    $data['action_id'] = $action_info['id'];
    $data['user_id'] = $user_id;
    $data['action_ip'] = get_client_ip();
    $data['model'] = $model;
    $data['record_id'] = $record_id;
    $data['log_source'] = $log_source;
    $data['create_time'] = NOW_TIME;

    //解析日志规则,生成日志备注
    if (!empty($action_info['log'])) {
        if (preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)) {
            $log['user'] = $user_id;
            $log['record'] = $record_id;
            $log['model'] = $model;
            $log['time'] = NOW_TIME;
            $log['data'] = array('user' => $user_id, 'model' => $model, 'record' => $record_id, 'time' => NOW_TIME);
            $replace = array();
            //执行第一个函数
            foreach ($match[1] as $value) {
                $param = explode('|', $value);
                if (isset($param[1])) {
                    array_push($replace, call_user_func($param[1], $log[$param[0]]));
                } else {
                    array_push($replace, $log[$param[0]]);
                }
            }
            //////执行第二个函数
            if (isset($match[2])) {
                foreach ($match[2] as $name) {
                    $param = explode('|', $name);
                    if (isset($param[1])) {
                        array_push($replace, call_user_func($param[1], $log[$param[0]]));
                    } else {
                        array_push($replace, $log[$param[0]]);
                    }
                }
            }

            $data['remark'] = str_replace($match[0], $replace, $action_info['log']);

        } else {
            $data['remark'] = $action_info['log'];
        }
    } else {
        //未定义日志规则，记录操作url
        $data['remark'] = '操作url：' . $_SERVER['REQUEST_URI'];
    }

    M('ActionLog')->add($data);

    if (!empty($action_info['rule'])) {
        //解析行为
        $rules = parse_action($action, $user_id);

        //执行行为
        $res = execute_action($rules, $action_info['id'], $user_id);
    }
}

/**
 * 根据ID和PID返回一个树形结构
 */
function list_to_tree2($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[$data['id']] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][$data['id']] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 解析模型中选项字段的分解
 */
function model_field_attr($str, $estr1 = '|', $estr2 = ':')
{
    $arr1 = array();
    $arr1 = explode($estr1, $str);
    if (count($arr1) > 0) {
        foreach ($arr1 as $arr1_one) {
            $arr2 = array();
            $arr2 = explode($estr2, $arr1_one);
            if (count($arr2) > 0) {
                $strarr[$arr2[0]] = $arr2[1];
            }
        }
    }
    return $strarr;
}

/**
 * 处理插件钩子
 * @param string $hook 钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook, $params = array())
{
    \Think\Hook::listen($hook, $params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name)
{
    $class = "Addons\\{$name}\\{$name}Addon";
    return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name)
{
    $class = get_addon_class($name);
    if (class_exists($class)) {
        $addon = new $class();
        return $addon->getConfig();
    } else {
        return array();
    }
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function addons_url($url, $param = array())
{
    $url = parse_url($url);
    $case = C('URL_CASE_INSENSITIVE');
    $addons = $case ? parse_name($url['scheme']) : $url['scheme'];
    $controller = $case ? parse_name($url['host']) : $url['host'];
    $action = trim($case ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    /* 基础参数 */
    $params = array(
        '_addons' => $addons,
        '_controller' => $controller,
        '_action' => $action,
    );
    $params = array_merge($params, $param); //添加额外参数

    return U('Addons/execute', $params);
}

/*文件夹操作*/

/**
 * 建立文件夹
 *
 * @param string $aimUrl
 * @return viod
 */
function createDir($aimUrl)
{
    $aimUrl = str_replace('', '/', $aimUrl);
    $aimDir = '';
    $arr = explode('/', $aimUrl);
    $result = true;
    foreach ($arr as $str) {
        $aimDir .= $str . '/';
        if (!file_exists($aimDir)) {
            $result = mkdir($aimDir);
        }
    }
    return $result;
}

/**
 * 建立文件
 *
 * @param string $aimUrl
 * @param boolean $overWrite 该参数控制是否覆盖原文件
 * @return boolean
 */
function createFile($aimUrl, $overWrite = false)
{
    if (file_exists($aimUrl) && $overWrite == false) {
        return false;
    } elseif (file_exists($aimUrl) && $overWrite == true) {
        unlinkFile($aimUrl);
    }
    $aimDir = dirname($aimUrl);
    createDir($aimDir);
    touch($aimUrl);
    return true;
}

/**
 * 移动文件夹
 *
 * @param string $oldDir
 * @param string $aimDir
 * @param boolean $overWrite 该参数控制是否覆盖原文件
 * @return boolean
 */
function moveDir($oldDir, $aimDir, $overWrite = false)
{
    $aimDir = str_replace('', '/', $aimDir);
    $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
    $oldDir = str_replace('', '/', $oldDir);
    $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
    if (!is_dir($oldDir)) {
        return false;
    }
    if (!file_exists($aimDir)) {
        createDir($aimDir);
    }
    @ $dirHandle = opendir($oldDir);
    if (!$dirHandle) {
        return false;
    }
    while (false !== ($file = readdir($dirHandle))) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (!is_dir($oldDir . $file)) {
            moveFile($oldDir . $file, $aimDir . $file, $overWrite);
        } else {
            moveDir($oldDir . $file, $aimDir . $file, $overWrite);
        }
    }
    closedir($dirHandle);
    return rmdir($oldDir);
}

/**
 * 移动文件
 *
 * @param string $fileUrl
 * @param string $aimUrl
 * @param boolean $overWrite 该参数控制是否覆盖原文件
 * @return boolean
 */
function moveFile($fileUrl, $aimUrl, $overWrite = false)
{
    if (!file_exists($fileUrl)) {
        return false;
    }
    if (file_exists($aimUrl) && $overWrite = false) {
        return false;
    } elseif (file_exists($aimUrl) && $overWrite = true) {
        unlinkFile($aimUrl);
    }
    $aimDir = dirname($aimUrl);
    createDir($aimDir);
    rename($fileUrl, $aimUrl);
    return true;
}

/**
 * 删除文件夹
 *
 * @param string $aimDir
 * @return boolean
 */
function unlinkDir($aimDir)
{
    $aimDir = str_replace('', '/', $aimDir);
    $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
    if (!is_dir($aimDir)) {
        return false;
    }
    $dirHandle = opendir($aimDir);
    while (false !== ($file = readdir($dirHandle))) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (!is_dir($aimDir . $file)) {
            unlinkFile($aimDir . $file);
        } else {
            unlinkDir($aimDir . $file);
        }
    }
    closedir($dirHandle);
    return rmdir($aimDir);
}

/**
 * 删除文件
 *
 * @param string $aimUrl
 * @return boolean
 */
function unlinkFile($aimUrl)
{
    if (file_exists($aimUrl)) {
        unlink($aimUrl);
        return true;
    } else {
        return false;
    }
}

/**
 * 复制文件夹
 *
 * @param string $oldDir
 * @param string $aimDir
 * @param boolean $overWrite 该参数控制是否覆盖原文件
 * @return boolean
 */
function copyDir($oldDir, $aimDir, $overWrite = false)
{
    $aimDir = str_replace('', '/', $aimDir);
    $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
    $oldDir = str_replace('', '/', $oldDir);
    $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
    if (!is_dir($oldDir)) {
        return false;
    }
    if (!file_exists($aimDir)) {
        createDir($aimDir);
    }
    $dirHandle = opendir($oldDir);
    while (false !== ($file = readdir($dirHandle))) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (!is_dir($oldDir . $file)) {
            copyFile($oldDir . $file, $aimDir . $file, $overWrite);
        } else {
            copyDir($oldDir . $file, $aimDir . $file, $overWrite);
        }
    }
    return closedir($dirHandle);
}

/**
 * 复制文件
 *
 * @param string $fileUrl
 * @param string $aimUrl
 * @param boolean $overWrite 该参数控制是否覆盖原文件
 * @return boolean
 */
function copyFile($fileUrl, $aimUrl, $overWrite = false)
{
    if (!file_exists($fileUrl)) {
        return false;
    }
    if (file_exists($aimUrl) && $overWrite == false) {
        return false;
    } elseif (file_exists($aimUrl) && $overWrite == true) {
        unlinkFile($aimUrl);
    }
    $aimDir = dirname($aimUrl);
    createDir($aimDir);
    copy($fileUrl, $aimUrl);
    return true;
}


function getParam()
{
    if (IS_POST) {
        return I("post.");
    } else {
        return I("get.");
    }
}

/**
 * 打印日志
 * @param $data
 */
function DLog($data)
{
    Think\Log::write(print_r($data, true), 'DEBUG');
}

function currentDate($time = 0)
{
    return date("Y-m-d H:i:s", $time ? $time : time());
}

function parseToIndex($array, $indexName, $deleteIndex = false)
{
    $newArray = array();
    foreach ($array as $item) {
        $key = $item[$indexName];
        if ($deleteIndex) {
            unset($item[$indexName]);
        }
        $newArray[$key] = $item;
    }
    return $newArray;
}

function p($data)
{
    echo "<pre>";
    print_r($data);
}
function successData($data){
    $res['Code'] = 200;
    $res['Data'] = $data;
}
function errorData($msg){
    $res['Code'] = 400;
    $res['Msg'] = $msg;
}