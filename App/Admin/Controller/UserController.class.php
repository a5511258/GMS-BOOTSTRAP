<?php 
/*
 * 用户控制器
 * Auth   : Ghj
 * Time   : 2016年01月10日 
 * QQ     : 912524639
 * Email  : 912524639@qq.com
 * Site   : http://guanblog.sinaapp.com/
 */
 
namespace Admin\Controller;


use Think\Log;

class UserController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('User');
    }
	
    /* 列表(默认首页)
     * Auth   : Ghj
     * Time   : 2016年01月10日 
     **/
	public function index(){
		if (IS_POST) {
			$post_data = I ( 'post.' );
			$post_data ['first'] = $post_data ['rows'] * ($post_data ['page'] - 1);
//			$map = array ();
            $map = $this->_search();

            $accout_state = $this->UserInfo['system_user'];
            if($accout_state > 0){
                $map ['system_user'] = 1;

                $map['_logic'] = "and";

            }
            else{
                $map ['system_user'] = array('in',"1,0");
            }



			$dao = M('user_group_view');
        	$map['status']= array('gt',-1);
			$total = $dao->where ( $map )->count ();
			if ($total == 0) {
				$_list = '';
			} else {
				$_list = $dao->where ( $map )->order ( $post_data ['sort'] . ' ' . $post_data ['order'] )->limit ( $post_data ['first'] . ',' . $post_data ['rows'] )->select ();
			}
			$data = array (
					'total' => $total,
					'rows' => $_list 
			);
			$this->ajaxReturn ( $data );
		}
		else {
			$this->display ();
		}
	}
	
    /* 搜索
     * Auth   : Ghj
     * Time   : 2016年01月10日 
     **/
	protected function _search() {
		$map = array ();
		$post_data=I('post.');
		/* 名称：用户名 字段：username 类型：string*/
		if(isset($post_data['s_username']) && $post_data['s_username']!=''){
			$map['id']=array('in', $post_data['s_username']);
		}
//		/* 名称：昵称/姓名 字段：nickname 类型：string*/
//		if($post_data['s_nickname']!=''){
//			$map['nickname']=array('like', '%'.$post_data['s_nickname'].'%');
//		}
//		/* 名称：邮箱 字段：email 类型：string*/
//		if($post_data['s_email']!=''){
//			$map['email']=array('like', '%'.$post_data['s_email'].'%');
//		}
//		/* 名称：余额 字段：amount 类型：num*/
//		if($post_data['s_amount']!=''){
//			$map['amount']=$post_data['s_amount'];
//		}
//		/* 名称：积分 字段：point 类型：num*/
//		if($post_data['s_point']!=''){
//			$map['point']=$post_data['s_point'];
//		}
//		/* 名称：创建时间 字段：create_time 类型：datetime*/
//		if($post_data['s_create_time_min']!=''){
//			$map['create_time'][]=array('gt',strtotime($post_data['s_create_time_min']));
//		}
//		if($post_data['s_create_time_max']!=''){
//			$map['create_time'][]=array('lt',strtotime($post_data['s_create_time_max']));
//		}
//		/* 名称：更新时间 字段：update_time 类型：datetime*/
//		if($post_data['s_update_time_min']!=''){
//			$map['update_time'][]=array('gt',strtotime($post_data['s_update_time_min']));
//		}
//		if($post_data['s_update_time_max']!=''){
//			$map['update_time'][]=array('lt',strtotime($post_data['s_update_time_max']));
//		}
//		/* 名称：状态 字段：status 类型：select*/
//		if($post_data['s_status']!=''){
//			$map['status']=$post_data['s_status'];
//		}
		return $map;
	}


    /**
     * 图片上传文件名称处理
     * @return string   返回文件名称字符串
     */

    protected function fileUpload(){
        $result = "";
        if(0 == intval($_FILES['user_photo']['size'])){
            return $result;
        }
        else{
            $fileInfo = $_FILES['user_photo'];
            $typeArr = explode("/", $fileInfo['type']);
            if (strtolower($typeArr[0]) !== "image") {
                return $result;
            }
            $suffix = "." . $typeArr[1];
            $fileName = md5_file($fileInfo["tmp_name"]) . $suffix;
            $path = UPLOAD_PATH. $fileName;
            $states = true;
            if (!file_exists($path)) {
                $states = move_uploaded_file($fileInfo["tmp_name"], $path);
            }
            if ($states) {
                $result = $fileName;
            }
        }
        return $result;
    }



    /* 添加
     * Auth   : Ghj
     * Time   : 2016年01月10日 
     **/
	public function add(){

        $result = array();
        $result['Result'] = false;
        $result['Code'] = 200;

		if(IS_POST){

			$post_data=I('post.');

            //判断上传图片大小是否为0
            if(0 != intval($_FILES['user_photo']['size'])){
                $post_data['head_img'] = $this->fileUpload();
            }
            else{
                $post_data['head_img'] = "";
            }


            unset($post_data['id']);

			$data=$this->Model->create($post_data);
			if($data){

				if($post_data['password'] === $post_data['eque_password']){
					$res = $this->Model->add($data);
					if(false !== $res){
						action_log('Add_User', 'User', $res);
                        $result['Result'] = true;
                        $result['Msg'] = '数据添加成功';
//						$this->success ( "操作成功！",U('index'));
					}else{
						$error = $this->Model->getError();
//						$this->error($error ? $error : "操作失败！");

                        $result['Msg'] = $error ? $error : "操作失败！";
					}
				}
				else{
					$error = $this->Model->getError();
//					$this->error($error ? $error : "密码与确认密码不相同！");
                    $result['Msg'] = $error ? $error : "操作失败！";
				}
			}
			else{
                $error = $this->Model->getError();
//                $this->error($error ? $error : "操作失败！");

                $result['Msg'] = $error ? $error : "操作失败！";
			}

            echo json_encode($result);
//			$this->ajaxReturn($result);
		}
	}
	
    /* 编辑
     * Auth   : Ghj
     * Time   : 2016年01月10日 
     **/
	public function edit(){
		if(IS_POST) {
            $post_data = I('post.');
            $result = array();
            $result['Result'] = false;
            $result['Code'] = 200;


            //判断上传图片大小是否为0
            if(0 != intval($_FILES['user_photo']['size'])){
                $post_data['head_img'] = $this->fileUpload();
            }
            else{
                $post_data['head_img'] = $this->Model->where(array(
                    'id' => $post_data['id']
                ))->field('head_img')->find()['head_img'];
            }

            $data = $this->Model->create($post_data);
            if ($data) {

                unset($data['password']);
                $res = $this->Model->where(array('id' => $post_data['id']))->save($data);
                if (false !== $res) {

                    $result['Result'] = true;
                    $result['Msg'] = '更新成功';
                    action_log('Edit_User', 'User', $post_data['id']);
//                    $this->success("操作成功！", U('index'));
                } else {
                    $error = $this->Model->getError();
//                    $this->error($error ? $error : "操作失败！");
                    $result['Msg'] = $error ? $error : "操作失败！";
                }
            }
            else {
                $error = $this->Model->getError();
//                $this->error($error ? $error : "操作失败！");
                $result['Msg'] = $error ? $error : "操作失败！";
            }

//            $this->ajaxReturn($result);
                echo json_encode($result);
        }

	}

	public function userinfo(){
        if(IS_POST){
            $dao = M('user');
            $data = null;
            $map = array ();
            $post_data=I('post.');
            if($post_data['id']!=''){
                $map['id']=array('in', $post_data['id']);
            }
            else{
                $map['id']=array('in', $this->UserInfo['id']);
            }
            $data = $dao->where ( $map )->field(
                'id,username,email,phone,head_img,status,group_ids,service_group_id,remark'
            )->find();

            $result['Code'] = 200;
            $result['Result'] = $data;
            echo json_encode($result);
        }
        else{
            $dao = M('user_group_view');
            $_info=$this->UserInfo;
            $_info = $dao->where(array('id'=>$_info['id']))->find();
            $this->assign('_info', $_info);
            $this->display();
        }


	}

//
//    /* 角色组
//     * Auth   : Ghj
//     * Time   : 2016年01月10日
//     **/
//	public function group(){
//		if(IS_POST){
//			$post_data=I('post.');
//			$group_ids=I('post.group_ids');
//			$_data['group_ids']=implode(',',$group_ids);
//			$this->Model->where(array('id'=>$post_data['id']))->save($_data);
//			$this->success ( "操作成功！",U('index'));
//		}else{
//			$_info=I('get.');
//			$_group_ids = $this->Model->where(array('id'=>$_info['id']))->getField('group_ids');
//			$this->assign('_info', $_info);
//			$this->assign('_group_id', $_group_ids);
//        	$this->display();
//		}
//	}
	
    /* 删除
     * Auth   : Ghj
     * Time   : 2016年01月10日 
     **/
	public function del(){
		$id=I('get.id');
		empty($id)&&$this->error('参数不能为空！');
		$res=$this->Model->delete($id);
		if(!$res){
			$this->error($this->Model->getError());
		}else{
			action_log('Del_User', 'User', $id);
			$this->success('删除成功！');
		}
	}

    /**
     * 修改密码
     */
    public function updatePassword(){
		if(IS_POST){

		    $result = array();
            $result['Code'] = 200;
            $result['Result'] = false;
			$post_data=I('post.');
            if('' != $post_data['old_password']){

                if('' != $post_data['new_password']){

                    if('' != $post_data['eque_password']){

                        if($post_data['eque_password'] == $post_data['new_password']){

                            $_info = $this->Model->where(array('id'=>$this->UserInfo['id']))->find();
                            if($_info['password'] == md5($post_data['old_password'])){


//                                $post_data['password']=md5($post_data['new_password']);此处重复加密,故而注释
                                
                                $post_data['password']=$post_data['new_password'];
                                $data=$this->Model->create($post_data);
                                if($data){

                                    unset($data['id']);

                                    $res = $this->Model->where(array('id'=>$post_data['id']))->save($data);
                                    if(false !== $res){

                                        action_log('Edit_User', 'User', $post_data['id']);

                                        $result['Result'] = true;

                                        $result['Msg'] = '重置密码成功';


//                                        $this->success ( "操作成功！",U('index'));
                                    }else{
//                                        $error = $this->Model->getError();
//                                        $this->error($error ? $error : "操作失败！");
                                        $result['Msg'] = '操作失败';

                                    }
                                }
                                else{
//                                    $error = $this->Model->getError();
//                                    $this->error($error ? $error : "操作失败！");

                                    $result['Msg'] = '操作失败';
                                }

                            }
                            else{
                                $result['Msg'] = '原密码错误';
                            }
                        }
                        else{
                            $result['Msg'] = '两次输入的密码不一致';
                        }
                    }
                    else{
                        $result['Msg'] = '请输入确认密码';
                    }

                }
                else{

                    $result['Msg'] = '请输入新密码';

                }
            }
            else{

                $result['Msg'] = '请输入原密码';

            }


            echo json_encode($result);
		}

    }

    public function updatePassword1(){
        if(IS_POST) {

            $result = array();
            $result['Code'] = 200;
            $result['Result'] = false;
            $post_data = I('post.');

            if ('' != $post_data['new_password']) {

                if ('' != $post_data['eque_password']) {

                    if ($post_data['eque_password'] == $post_data['new_password']) {


                        $post_data['password'] = $post_data['new_password'];
                        $data = $this->Model->create($post_data);
                        if ($data) {

                            unset($data['id']);

                            $res = $this->Model->where(array('id' => $post_data['id']))->save($data);
                            if (false !== $res) {

                                action_log('Edit_User', 'User', $post_data['id']);

                                $result['Result'] = true;

                                $result['Msg'] = '更新密码成功';

                            } else {

                                $result['Msg'] = '操作失败';

                            }
                        } else {

                            $result['Msg'] = '操作失败';
                        }

                    } else {
                        $result['Msg'] = '两次输入的密码不一致';
                    }
                } else {
                    $result['Msg'] = '请输入确认密码';
                }

            } else {

                $result['Msg'] = '请输入新密码';

            }


            echo json_encode($result);
        }
    }
}