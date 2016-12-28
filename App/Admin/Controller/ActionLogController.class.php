<?php 
/*
 * 行为日志控制器
 * Auth   : Ghj
 * Time   : 2016年01月13日 
 * QQ     : 912524639
 * Email  : 912524639@qq.com
 * Site   : http://guanblog.sinaapp.com/
 */
 
namespace Admin\Controller;
use Admin\Model\UserModel;
use Service\Utils\GroupInfoUtils;

class ActionLogController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('ActionLog');
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


            $map['_logic'] = 'and';
			$total = $this->Model->where ( $map )->count ();
			if ($total == 0) {
				$_list = '';
			} else {
				$_list = $this->Model->where ( $map )->order ( $post_data ['sort'] . ' ' . $post_data ['order'] )->limit ( $post_data ['first'] . ',' . $post_data ['rows'] )->select ();
			}
			if($total > 0){
                foreach($_list as $list_key=>$list_one){
                    $_list[$list_key]['action_id_show']=get_action($_list [$list_key]['action_id'],'title');
                    $_list[$list_key]['user_id_show']=get_username($_list [$list_key]['user_id']);
                    $content= $_list[$list_key]['remark'];
                    $_list[$list_key]['remark']="<div style='white-space: pre-wrap;word-break: break-all'>$content</div>";

                }
            }
			$data = array (
					'total' => $total,
					'rows' => $_list 
			);
			$this->ajaxReturn ( $data );
		} else {
        	$this->meta_title = '模型列表';
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
		/* 名称：行为id 字段：action_id 类型：string*/
		if($post_data['s_action_id']!=''){
//			$map['action_id']=array('like', '%'.$post_data['s_action_id'].'%');
            $map['action_id']=array('in', $post_data['s_action_id']);
		}
		/* 名称：执行用户id 字段：user_id 类型：string*/
		if($post_data['s_user_id']!=''){
//			$map['user_id']=array('like', '%'.$post_data['s_user_id'].'%');

            $map['user_id']=array('in', $post_data['s_user_id']);
		}
		else{
            //差别显示
            $accout_group = $this->UserInfo['service_group_id'];
            $Utils = new GroupInfoUtils();
            $group_ids = $Utils->getAllGroupID($accout_group);
            $dao_usr = M('user');
            $mapuser['service_group_id'] = array('in',$group_ids);
            $users_ids = $dao_usr->where($mapuser)->field('id')->select();
            $ids_array = array();
            foreach ($users_ids as $ids){
                array_push($ids_array,$ids['id']);
            }
            $ids_array = array_unique($ids_array);

            $str = implode(',', $ids_array);

            $map['user_id'] = array('in',$str);
        }
		/* 名称：执行行为的时间 字段：create_time 类型：datetime*/
		if($post_data['s_create_time_min']!=''){
			$map['create_time'][]=array('gt',strtotime($post_data['s_create_time_min']));
		}
		if($post_data['s_create_time_max']!=''){
			$map['create_time'][]=array('lt',strtotime($post_data['s_create_time_max']));
		}
		return $map;
	}
    
    /* 添加
     * Auth   : Ghj
     * Time   : 2016年01月13日 
     **/
	public function add(){
		if(IS_POST){
			$post_data=I('post.');
 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->add($data);
				if($result){
					action_log('Add_ActionLog', 'ActionLog', $result);
					$this->success ( "操作成功！",U('index'));
				}else{
					$error = $this->Model->getError();
					$this->error($error ? $error : "操作失败！");
				}
			}else{
                $error = $this->Model->getError();
                $this->error($error ? $error : "操作失败！");
			}
		}else{
        	$this->display();
		}
	}
	
    /* 编辑
     * Auth   : Ghj
     * Time   : 2016年01月13日 
     **/
	public function edit(){
		if(IS_POST){
			$post_data=I('post.');
 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->where(array('id'=>$post_data['id']))->save($data);
				if($result){
					action_log('Edit_ActionLog', 'ActionLog', $post_data['id']);
					$this->success ( "操作成功！",U('index'));
				}else{
					$error = $this->Model->getError();
					$this->error($error ? $error : "操作失败！");
				}
			}else{
                $error = $this->Model->getError();
                $this->error($error ? $error : "操作失败！");
			}
		}else{
			$_info=I('get.');
			$_info = $this->Model->where(array('id'=>$_info['id']))->find();
			$userModel=new UserModel();
			$user=$userModel->find($_info['user_id']);
			$_info["user_id"]=$user["username"];
			$this->assign('_info', $_info);
        	$this->display();
		}
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
			action_log('Del_ActionLog', 'ActionLog', $id);
			$this->success('删除成功！');
		}
	}
}