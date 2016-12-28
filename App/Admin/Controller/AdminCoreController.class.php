<?php
namespace Admin\Controller;
use Common\Controller\CoreController;

class AdminCoreController extends CoreController {
	//后台核心继承
    protected function _initialize() {
		//继承CoreController的初始化函数
        parent::_initialize();
		$AUTH_KEY=session(C('AUTH_KEY'));
		//判断认证key如果小于1 或 Admin模块登录Key不为1，跳转到后台登录网关
		if( $AUTH_KEY < 1 ) {
			redirect(U(C('AUTH_USER_GATEWAY')));
		}else{
//			if(!in_array($this->UserInfo['group_id'],array(1,2)) && !(in_array ( session ( C ( 'AUTH_KEY' ) ), C ( 'AUTH_ADMIN' ) ))){
//				$this->error ( '你不是管理员组用户所以无法登录！' ,U('Public/login'));
//			}
			//判断当前模块是否为非认证模块
			$Auth_Rule = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
			if (!Is_Auth($Auth_Rule)) {
				$this->error ( '你没有权限进行 ' . $Auth_Rule . ' 操作！' );
			}
		}
	}

	//后台菜单
	protected function get_menu(){
		//获取后台菜单缓存
		$AdminMenu=session('AdminMenu');
		//如果缓存为空，即初次登录
		if(count($AdminMenu)!=999){
			
			if (in_array ( session ( C ( 'AUTH_KEY' ) ), C ( 'AUTH_ADMIN' ) )) {//如果认证key存在超级管理组配置中,不读取用户权限直接读取全部可显示菜单
				$map = array (
						'hide' => 0,
						'status' => 1 
				);
			} else {//如果认证key不存在超级管理组配置中,读取用户权限,根据权限获取用户组
				//实例化Auth权限管理类
				$Auth = new \Common\Libs\Auth();
				//获取当前用户 所在的所有组（即一个用户可以存在于多个用户组中）
				$groups = $Auth->getGroups(session(C('AUTH_KEY')));
				$ids = array ();
				if(count($groups)<1){
					$this->error ( '你没有系统的任何权限！',U('Public/logout'));
				}
				foreach ( $groups as $g ) {
					$ids = array_merge ( $ids, explode ( ',', trim ( $g ['rules'], ',' ) ) );
				}
				$ids = array_unique ( $ids );
				$map = array (
						'id' => array ('in',$ids ),
						'hide' => 0,
						'status' => 1 
				);
			}
			//根据前面生成的查询条件 读取用户组所有权限规则
			$rules = M ( 'AuthRule' )->where ( $map )->field ( 'id,pid,name,title,icon,menu_type' )->order ( 'sort asc' )->select ();
			foreach ( $rules as $rid=>$r_one ) {
				$rules[$rid]['url']=U($r_one['name']);
			}
			
			
			$AdminMenu = list_to_tree2 ( $rules, $pk = 'id', $pid = 'pid', 'children' );
			session ( 'AdminMenu', null );
			session('AdminMenu',$AdminMenu);
		}
		return $AdminMenu;
	}
}