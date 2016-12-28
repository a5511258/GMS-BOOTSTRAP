<?php 
/*
 * 用户组控制器
 * Auth   : Ghj
 * Time   : 2016年01月13日 
 * QQ     : 912524639
 * Email  : 912524639@qq.com
 * Site   : http://guanblog.sinaapp.com/
 */
 
namespace Admin\Controller;

class AuthGroupController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('AuthGroup');
    }
	
    /* 列表(默认首页)
     * Auth   : Ghj
     * Time   : 2016年01月13日 
     **/
	public function index(){
		if (IS_POST) {

			$post_data = I ( 'post.' );
			$post_data ['first'] = $post_data ['rows'] * ($post_data ['page'] - 1);

            $map = $this->_search();

        	$map['status'] = array('gt',-1);


            //区别显示
            $accout_state = $this->UserInfo['system_user'];
            if($accout_state > 0){
                $map ['editable'] = 1;
                $map['visible']= 1;
                $map ['_logic'] = 'and';
            }

			$total = $this->Model->where ( $map )->count ();
			if ($total == 0) {
				$_list = '';
			} else {
				$_list = $this->Model->where ( $map )->order ( $post_data ['sort'] . ' ' . $post_data ['order'] )->limit ( $post_data ['first'] . ',' . $post_data ['rows'] )->select ();
			}
			$data = array (
					'total' => $total,
					'rows' => $_list 
			);
			$this->ajaxReturn ( $data );
		} else {
			$this->display ();
		}
	}
	
    /* 搜索
     * Auth   : Ghj
     * Time   : 2016年01月13日 
     **/
	protected function _search() {
		$map = array ();
		$post_data=I('post.');
		/* 名称：用户组标题 字段：title 类型：string*/
		if($post_data['s_title']!=''){
			$map['title']=array('like', '%'.$post_data['s_title'].'%');
		}
		/* 名称：用户组状态 字段：status 类型：select*/
//		if($post_data['s_status']!=''){
//			$map['status']=array('in', $post_data['s_status']);
//		}
		return $map;
	}
    
    /* 添加
     * Auth   : Ghj
     * Time   : 2016年01月13日 
     **/
	public function add(){

        $result['Code'] = 200;

        $result['Result'] = false;

        $result['Msg'] = '';


		if(IS_POST){
			$post_data=I('post.');

//			$post_data["rules"]=I("post.rules");

			$post_data["rules"]=implode(",",$post_data["rules"]);

            if('' != $post_data["rules"]){
                $data=$this->Model->create($post_data);
                if($data){

                    unset($data['id']);

                    $res = $this->Model->add($data);
                    if(false !== $res){
                        action_log('Add_AuthGroup', 'AuthGroup', $result);

                        $result['Result'] = true;

                        $result['Msg'] = '角色添加成功';

                    }else{
                        $error = $this->Model->getError();
//					$this->error($error ? $error : "操作失败！");
                        $result['Msg'] = $error ? $error : "操作失败！";
                    }
                }
                else{
                    $error = $this->Model->getError();

                    $result['Msg'] = $error ? $error : "操作失败！";
                }
            }
            else{
                $result['Msg'] = '角色权限不能为空';
            }


		}

		echo json_encode($result);

//        $this->ajaxReturn($result);

	}
	
    /* 编辑
     * Auth   : Ghj
     * Time   : 2016年01月13日 
     **/
	public function edit(){

        $result['Code'] = 200;

        $result['Result'] = false;

        $result['Msg'] = '';

		if(IS_POST){
			$post_data=I('post.');


//			$post_data["rules"]=I("post.rules");

			$post_data["rules"]=implode(",",$post_data["rules"]);

//            dump($post_data);

			$data=$this->Model->create($post_data);
			if($data){
			    unset($data['id']);
				$res = $this->Model->where(array('id'=>$post_data['id']))->save($data);
				if(false !== $res){

                    $result['Result'] = true;

                    $result['Msg'] = '角色更新成功';

					action_log('Edit_AuthGroup', 'AuthGroup', $post_data['id']);
//					$this->success ( "操作成功！",U('index'));
				}else{
					$error = $this->Model->getError();
//					$this->error($error ? $error : "操作失败！");

                    $result['Msg'] = $error ? $error : "操作失败！";

				}
			}
			else{
                $error = $this->Model->getError();
//                $this->error($error ? $error : "操作失败！");
                $result['Msg'] = $error ? $error : "操作失败！";

			}
		}
		else{
			$_info=I('get.');
			$_info = $this->Model->where(array('id'=>$_info['id']))->field('id,title,rules,sort,status')->find();


            $result['Result'] = $_info;

//			$this->assign('_info', $_info);
//        	$this->display();
		}
        $this->ajaxReturn($result);
	}
	
    /* 删除
     * Auth   : Ghj
     * Time   : 2016年01月13日 
     **/
	public function del(){
		$id=I('get.id');
		empty($id)&&$this->error('参数不能为空！');
		$res=$this->Model->delete($id);
		if(!$res){
			$this->error($this->Model->getError());
		}else{
			action_log('Del_AuthGroup', 'AuthGroup', $id);
			$this->success('删除成功！');
		}
	}
}