<?php

namespace Admin\Controller;

use Common\Controller\CoreController;

class PublicController extends CoreController
{

    public function login($map = '')
    {
        $login_state = array();
        if (IS_POST || $map != '') {
            if ($map == '') {
                $username = I("post.username", "", "trim");
                $password = md5(I("post.password", "", "trim"));
                if (empty ($username) || empty ($password)) {
                    //用户名或密码为空
                    $this->ajaxReturn(errorData('用户名或密码不能为空！'));
                }
                $map = array(
                    'username' => $username,
                    'password' => $password,
//						'status' => 1
                );
            }
            //查询用户ID ，密码 头像、角色、 组织ID 、状态、系统管理员标记
            $UserInfo = M('User')->where($map)
                ->field('id,username,head_img,group_ids as group_id,service_group_id,status,system_user')
                ->find();
            if ($UserInfo) {
                //记录用户登录日志
                action_log('Admin_Login', 'User', $UserInfo ['id']);
                if ("1" == $UserInfo['status']) {
                    //检索登录用户权限
                    $AG_Data = M('AuthGroup')->where(array('id' => $UserInfo['group_id']))->find();
                    $UserInfo['group_title'] = $AG_Data['title'];
                    //session存储登录用户ID
                    session(C('AUTH_KEY'), $UserInfo['id']);
                    //session存储登录用户UserInfo
                    session('UserInfo', $UserInfo);
                    //查询记住密码时间
                    if (C('?WEB_REMIND_ME')) {
                        $admin_reme = C('WEB_REMIND_ME');
                    } else {
                        $admin_reme = 3600;
                    }
                    if (I("post.rember_password")) {
                        cookie('rw', $map, $admin_reme);
                    }
                    $this->ajaxReturn(U(C('Admin/Index/index')));
                } else {
                    $this->ajaxReturn(errorData('此用户已被管理员禁用！，请联系管理员'));
                }
            } else {
                $this->ajaxReturn(errorData('用户名或密码错误！，请联系管理员'));

            }
        } else {
            if (is_login()) {
                $this->redirect(U(C('AUTH_USER_INDEX')));
            } else {
                $map = cookie('rw');
                if ((count($map) > 0)) {
                    $this->login($map);
                } else {
                    $this->display();
                }
            }
        }
    }


    /* 退出登录 */
    public function logout()
    {
        if (!is_login()) {
            $this->error("尚未登录", U(C('AUTH_USER_GATEWAY')));
        } else {
            action_log('Admin_Logout', 'User', is_login());
            session(null);
            cookie('rw', null);
            if (session(C('AUTH_KEY'))) {
                $this->error("退出失败", U(C('AUTH_USER_INDEX')));
            } else {
                header("Location: " . U(C('AUTH_USER_GATEWAY')));
            }
        }
    }

}
