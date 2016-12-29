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
                    echo $this->ajaxReturn($this->errorData('用户名或密码错误！'));
                }
                $map = array(
                    'username' => $username,
                    'password' => $password,
//						'status' => 1
                );
            }
            $UserInfo = M('User')->where($map)
                ->field('id,username,head_img,group_ids as group_id,service_group_id,status,system_user')
                ->find();
            if ($UserInfo) {
                $AG_Data = M('AuthGroup')->where(array('id' => $UserInfo['group_id']))->find();
                $UserInfo['group_title'] = $AG_Data['title'];

                session(C('AUTH_KEY'), $UserInfo['id']);
                session('UserInfo', $UserInfo);
                if (C('?ADMIN_REME')) {
                    $admin_reme = C('ADMIN_REME');
                } else {
                    $admin_reme = 3600;
                }
                if (I("post.rember_password")) {
                    cookie('rw', $map, $admin_reme);
                }
                action_log('Admin_Login', 'User', $UserInfo ['id']);


//                dump($UserInfo);


                if ("1" == $UserInfo['status']) {

                    $login_state['Code'] = 200;
                    $login_state['Url'] = U(C('Admin/Index/index'));

                } else {

                    $login_state['Code'] = 500;
                    $login_state['Url'] = U(C('AUTH_USER_GATEWAY'));
                }

                echo json_encode($login_state);


            } else {
                $login_state['Code'] = 400;
                $login_state['Url'] = U(C('AUTH_USER_GATEWAY'));
                echo json_encode($login_state);

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
