<?php

/*
 * 公共方法控制器
 * Auth : Ghj
 * Time : 2015年4月11日
 * QQ : 912524639
 * Email : 912524639@qq.com
 * Site : http://guanblog.sinaapp.com/
 */
namespace Admin\Controller;

use Common\Controller\CoreController;
use Service\Utils\GroupInfoUtils;

class FunctionController extends CoreController
{

    //核心继承
    protected function _initialize()
    {
        //继承CoreController的初始化函数
        parent::_initialize();
        if (session(C('AUTH_KEY'))) {
            return false;
        }
    }

    /*
     * 根据传入config的名称获取config然后返回
     * Auth : Ghj
     * Time : 2015年4月16日
     */
    public function get_config($cname = '')
    {
        if ($cname == '') {
            $cname = I('get.cname');
        }
        $extra_option_arr = explode('|', $cname);
        $ops = C($extra_option_arr [0]);
        if ($extra_option_arr[1] == '') {
            $ops_arr = explode('|', $ops);
            $data_ls ['type'] = '';
            $data_ls ['value'] = '请选择一个选项';
            $data [] = $data_ls;
            foreach ($ops_arr as $ops_arr_val) {
                $val_ls = explode(':', $ops_arr_val);
                $data_ls ['type'] = $val_ls[0];
                $data_ls ['value'] = $val_ls[1];
                $data [] = $data_ls;
            }
        } else {
            $data_ls [$extra_option_arr [1]] = '';
            $data_ls [$extra_option_arr [2]] = '请选择一个选项';
            $data [] = $data_ls;
            $ops_arr = explode('|', $ops);
            foreach ($ops_arr as $ops_arr_val) {
                $val_ls = explode(':', $ops_arr_val);
                $data_ls [$extra_option_arr [1]] = $val_ls[0];
                $data_ls [$extra_option_arr [2]] = $val_ls[1];
                $data [] = $data_ls;
            }
        }
        $r_type = I('get.r_type');
        if ($r_type == 'json') {
            $this->ajaxReturn($data);
        } else {
            return $data;
        }
    }

    /*
     * 获取用户组树
     * Auth : Ghj
     * Time : 2015年4月16日
     */
    public function get_auth_group($_pid = '0')
    {
        if ($_pid == '0') {
            $_pid = I('get.pid', 0);
        }
        $map ['status'] = 1;

        //差别显示
        $accout_state = $this->UserInfo['system_user'];
        if ($accout_state > 0) {
            $map ['editable'] = 1;
            $map ['_logic'] = 'and';
        }
        $_list = M('AuthGroup')->where($map)->order('id asc')->field('id,title as text')->select();
        $r_type = I('get.r_type');
        if ($r_type == 'json') {
            $this->ajaxReturn($_list);
        } else {
            return $_list;
        }
    }


    /*
     * 获取节点树
     * Auth : Ghj
     * Time : 2015年4月16日
     */
    public function get_auth_rule_extend($_pid = '0')
    {
        if ($_pid == '0') {
            $_pid = I('get.pid', 0);
        }
        $map ['status'] = 1;
        $_list = M('AuthRule')->where($map)->order('sort asc')->getField('id,pid,title as text,icon');

//		获取当前用户角色，移除其中的某些项

        foreach ($_list as $key => $_list_one) {
            $_list[$key]['iconCls'] = $_list_one['icon'];
        }
        if ($_pid == '-1') {
            $_list [] = array(
                'id' => '0',
                'pid' => '-1',
                'text' => '根节点',
                'iconCls' => 'iconfont icon-viewlist'
            );
            $data = list_to_tree($_list, 'id', 'pid', 'children', '-1');
        } else {
            $data = list_to_tree($_list, 'id', 'pid', 'children');
        }
        $r_type = I('get.r_type');
        if ($r_type == 'json') {
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    /*
     * 获取节点树
     * Auth : Ghj
     * Time : 2015年4月16日
     */
    public function get_auth_rule($_pid = '0')
    {
        if ($_pid == '0') {
            $_pid = I('get.pid', 0);
        }
        $map ['status'] = 1;
        $accout_state = $this->UserInfo['system_user'];
        if ($accout_state > 0) {
            $map ['system_jurisdiction'] = 1;
            $map ['_logic'] = 'and';
        }
        $_list = M('AuthRule')->where($map)->order('sort asc')->getField('id,pid,title as text');
//		foreach($_list as $key=>$_list_one){
//			$_list[$key]['iconCls']=$_list_one['icon'];
//            unset($_list[$key]['icon']);
//		}


//        dump($_list);

        if ($_pid == '-1') {
            $_list [] = array(
                'id' => '0',
                'pid' => '-1',
                'text' => '根节点',
//					'iconCls'=>'iconfont icon-viewlist'
            );
            $data = list_to_tree($_list, 'id', 'pid', 'children', '-1');
        } else {
            $data = list_to_tree($_list, 'id', 'pid', 'children');
        }
        $r_type = I('get.r_type');
        if ($r_type == 'json') {
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    /*
     * 获取图标
     * Auth : Ghj
     * Time : 2015年4月16日
     */
    public function get_icon($_pid = '0')
    {
        $iconfont = file_get_contents('./Public/Static/Font/iconfont.css');
        $preg = '/.(.*):before/U';
        preg_match_all($preg, $iconfont, $arr);
        foreach ($arr[1] as $one) {
            $data_ls['id'] = 'iconfont ' . $one;
            $data_ls['text'] = $one;
            $data[] = $data_ls;
        }
        $r_type = I('get.r_type');
        if ($r_type == 'json') {
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    /*
     * 获取指定字段类型配置文件
     * Auth : Ghj
     * Time : 2015年4月16日
     */
    public function field_setting($fieldtype = '')
    {
        if ($fieldtype == '') {
            $fieldtype = I('get.fieldtype', 'num');
        }
        $f_type = I('get.f_type', 'add');
        $field_config = C('FIELD_LIST'); // 获取字段类型列表
        $field = $field_config [$fieldtype] ['field'];
        $fiepath = MODULE_PATH . 'Fields/' . $fieldtype . '/';
        ob_start();
        include $fiepath . "field_" . $f_type . "_form.inc.php";
        $data_setting = $data_setting . ob_get_contents();
        ob_end_clean();
        $settings = array(
            'field' => $field,
            'extra' => $data_setting
        );
        $r_type = I('get.r_type');
        if ($r_type == 'json') {
            echo json_encode($settings);
        } else {
            return $settings;
        }
    }

    /*
     * 获取指定字段类型的相应字段数据类型配置组
     * Auth : Ghj
     * Time : 2015年4月16日
     */
    public function get_field_default($fieldtype = '')
    {
        if ($fieldtype == '') {
            $fieldtype = I('get.fieldtype', 'num');
        }
        $field_config = C('FIELD_LIST'); // 获取字段类型列表
        $field = $field_config [$fieldtype] ['field'];
        foreach ($field as $one) {
            $field_ls['id'] = $one;
            $field_ls['text'] = $one;
            $field_z[] = $field_ls;
        }
        echo json_encode($field_z);
    }

    /*
     * 根据传入ID获取字段配置中的参数中的option，并且解析
     * Auth : Ghj
     * Time : 2015年4月16日
     */
    public function get_field_option($f_id = '')
    {
        if ($f_id == '') {
            $f_id = I('get.f_id', 0);
        }
        $extra_ys = M('ModelField')->where('id=' . $f_id)->getField('extra');
        $extra = unserialize($extra_ys);
        $ops = model_field_attr($extra ['option']);
        foreach ($ops as $opkey => $opkeyval) {
            $data_ls ['key'] = $opkey;
            $data_ls ['val'] = $opkeyval;
            $data [] = $data_ls;
        }
        $r_type = I('get.r_type');
        if ($r_type == 'json') {
            $this->ajaxReturn($data);
        } else {
            return $data;
        }
    }


    public function get_status_option()
    {
//        op_status["0"] = "禁用"
//					op_status["1"] = "启用"
//					op_status["2"] = "审核中"


        $result = array();


        $result[0]['id'] = 0;
        $result[0]['text'] = "禁用";

        $result[1]['id'] = 1;
        $result[1]['text'] = "启用";
//
//        $result[2]['id'] = 2;
//        $result[2]['text'] = "审核中";
//        $result[2]['id'] = '';
//        $result[2]['text'] = "请选择";

        $this->ajaxReturn($result);

    }

    /*
     * 获取组织节点树
     * Auth : li
     * Time : 2016年08月05日09:45:42
     */

    public function getGroupInfosToTree()
    {
        $Utils = new GroupInfoUtils();

        $data = $Utils->getGroup($this->UserInfo['service_group_id']);

        $r = $Utils->formatarray($data);

        $temp_parent_id = $r[0]['ParentId'];


        //此处 easyui 默认图标与 iconfont 图标库冲突
        foreach ($r as $k => $v) {

            $r[$k]['iconCls'] = '';


            if ($v['ParentId'] < $temp_parent_id) {
                $temp_parent_id = $v['ParentId'];
            }
        }

        $result = list_to_tree($r, 'id', 'ParentId', 'children', $temp_parent_id);

        echo json_encode($result);
    }


    public function searchUser()
    {

        $accout_group = $this->UserInfo['service_group_id'];
        $Utils = new GroupInfoUtils();
        $group_ids = $Utils->getAllGroupID($accout_group);
        $dao_usr = M('user');
        $mapuser['service_group_id'] = array('in', $group_ids);
        $users_id = $dao_usr->where($mapuser)->field('id as word_id,username as word')->select();

        echo json_encode($users_id);

    }

    /**
     * 拉取行为列表
     */

    public function searchAction()
    {

        $dao_action = M('action');

        $data_action = $dao_action->field('id as word_id,title as word')->select();

        echo json_encode($data_action);

    }

    /**
     * 拉取角色列表
     */
    public function getAuthList()
    {

        $accout_state = $this->UserInfo['system_user'];
        if ($accout_state > 0) {
            $map ['visible'] = 1;
        } else {
            $map['visible'] = array('in', "1,0");
        }

        $dao = D('auth_group');
        $data = $dao->where($map)->field("id,title as text")->select();
        echo json_encode($data);
    }


}
