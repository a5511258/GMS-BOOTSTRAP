<?php 
/*
 * 配置模型控制器
 * Auth   : Ghj
 * Time   : 2016年01月09日 
 * QQ     : 912524639
 * Email  : 912524639@qq.com
 * Site   : http://guanblog.sinaapp.com/
 */
 
namespace Admin\Controller;

class ConfigController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('Config');
    }
	
    /* 列表(默认首页)
     * Auth   : Ghj
     * Time   : 2016年01月09日 
     **/
	public function index(){
		if (IS_POST) {
			$post_data = I ( 'post.' );
			$post_data ['first'] = $post_data ['rows'] * ($post_data ['page'] - 1);
			$map = array ();
        	$map = array('status'=>array('gt',-1));
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
     * Time   : 2016年01月09日 
     **/
	protected function _search() {
		$map = array ();
		$post_data=I('post.');
		/* 名称：配置名称 字段：name 类型：string*/
		if($post_data['s_name']!=''){
			$map['name']=array('like', '%'.$post_data['s_name'].'%');
		}
		/* 名称：配置类型 字段：type 类型：select*/
		if($post_data['s_type']!=''){
			$map['type']=$post_data['s_type'];
		}
		/* 名称：配置说明 字段：title 类型：string*/
		if($post_data['s_title']!=''){
			$map['title']=array('like', '%'.$post_data['s_title'].'%');
		}
		/* 名称：配置分组 字段：group 类型：select*/
		if($post_data['s_group']!=''){
			$map['group']=$post_data['s_group'];
		}
		/* 名称：状态 字段：status 类型：select*/
		if($post_data['s_status']!=''){
			$map['status']=$post_data['s_status'];
		}
		/* 名称：排序 字段：sort 类型：num*/
		if($post_data['s_sort']!=''){
			$map['sort']=$post_data['s_sort'];
		}
		return $map;
	}
    
    /* 添加
     * Auth   : Ghj
     * Time   : 2016年01月09日 
     **/
	public function add(){
		if(IS_POST){
			$post_data=I('post.');
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->add($data);
				if($result){
					action_log('Add_Config', 'Config', $result);
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
     * Time   : 2016年01月09日 
     **/
	public function edit(){
		if(IS_POST){
			$post_data=I('post.');
 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->where(array('id'=>$post_data['id']))->save($data);
				if($result){
					action_log('Edit_Config', 'Config', $post_data['id']);
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
			$this->assign('_info', $_info);
        	$this->display();
		}
	}
	
    /* 删除
     * Auth   : Ghj
     * Time   : 2016年01月09日 
     **/
	public function del(){
		$id=I('get.id');
		empty($id)&&$this->error('参数不能为空！');
		$res=$this->Model->delete($id);
		if(!$res){
			$this->error($this->Model->getError());
		}else{
			action_log('Del_Config', 'Config', $id);
			$this->success('删除成功！');
		}
	}
	/*
	 * 批量配置
	 * Auth : Ghj
	 * Time : 2015年06月20日
	 */
	public function group() {
		if (IS_POST) {
			$config = I ( 'post.config' );
			if ($config && is_array ( $config )) {
				foreach ( $config as $name => $value ) {
					$map = array ('name' => $name);
					M ( 'Config' )->where ( $map )->setField ( 'value', $value );
				}
			}
			$this->Model->cache();
			action_log('Group_Config', 'Config', I('get.id'));
			$this->success ( '保存成功！', U ( '?id=' . I ( 'get.id' ) ) );
		} else {
			$id = I ( 'get.id', 1 );
			$type = model_field_attr ( C ( 'CONFIG_GROUP_LIST' ) );
			$list = M ( "Config" )->where ( array ('status' => 1,'group' => $id) )->field ( 'id,name,title,extra,value,remark,type' )->order ( 'sort' )->select ();
			if ($list) {
				$this->assign ( 'list', $list );
			}
			$this->assign ( 'type', $type );
			$this->assign ( 'id', $id );
			$this->display ();
		}
	}
}