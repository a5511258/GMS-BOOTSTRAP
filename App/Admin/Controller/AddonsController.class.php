<?php 
/*
 * 插件控制器
 * Auth   : Ghj
 * Time   : 2016年01月24日 
 * QQ     : 912524639
 * Email  : 912524639@qq.com
 * Site   : http://guanblog.sinaapp.com/
 */
 
namespace Admin\Controller;

class AddonsController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('Addons');
    }
	
    /* 列表(默认首页)
     * Auth   : Ghj
     * Time   : 2016年01月24日 
     **/
	public function index(){
		if (IS_POST) {
			$_list = $this->Model->getList();
			$data = array (
					'total' => count($_list),
					'rows' => $_list 
			);
			$this->ajaxReturn ( $data );
		} else {
			$this->display ();
		}
	}
	
    /**
     * 模块启用 禁用
     */
    public function disabled() {
		$name=I('get.addon_name');
		$d=I('get.d',1);
		empty($name)&&$this->error('参数不能为空！');
		$res=$this->Model->where(array('name'=>$name))->save(array('disabled'=>$d));
		if(!$res){
			$error=$this->Model->getError();
			$this->error($error?$error:'操作失败');
		}else{
			S('hooks', null);
			$this->success('更改成功！');
		}
    }
	
    /**
     * 安装插件
     */
    public function install(){
		//获取需要安装插件的标识
        $addon_name     =   trim(I('get.addon_name'));
		//获取插件的类
        $class          =   get_addon_class($addon_name);
		//插件类不存在
        if(!class_exists($class)){
            $this->error('插件不存在');
		}
		//实例化插件类
        $addons  =   new $class;
		//获取插件的Info信息
        $info = $addons->info;
		//检测信息的正确性：如果信息不存在，或者插件类中的Info信息不完全
        if(!$info || !$addons->checkInfo()){
            $this->error('插件信息缺失');
		}
		//在session中设置插件的安装错误为空
        session('addons_install_error',null);
		//使用前面实例化过的插件类，进行install操作，且将返回的数据存储在$install_flag中
        $install_flag   =   $addons->install();
		//如果$install_flag为false
        if(!$install_flag){
			//返回错误信息，错误数据应该在插件安装的过程中存储在session中
            $this->error('执行插件预安装操作失败'.session('addons_install_error'));
        }
		//验证插件的Info信息
        $data = $this->Model->create($info);
        if(!$data){
            $this->error($this->Model->getError());
		}
		$data['status']=1;
		//将数据存储到插件模型中去
        if($this->Model->add($data)){
			//config为获取插件的config信息
            $config = array('config'=>json_encode($addons->getConfig()));
			//对指定的插件进行更新配置
            $this->Model->where("name='{$addon_name}'")->save($config);
			//更新钩子上有这个插件的数据
            $hooks_update   =   D('Hooks')->updateHooks($addon_name);
			copyDir('./App/Addons/'.$addon_name.'/style/'.$addon_name,'./Public/Addons/'.$addon_name,true);
            if($hooks_update){
                S('hooks', null);
                $this->success('安装成功');
            }else{
                $this->Model->where("name='{$addon_name}'")->delete();
                $this->error('更新钩子处插件失败,请卸载后尝试重新安装');
            }
        }else{
            $this->error('写入插件数据失败');
        }
    }

    /**
     * 卸载插件
     */
    public function uninstall(){
		//获取卸载插件的ID
		$name=I('get.addon_name');
		//读取卸载插件的数据库信息
        $db_addons      =   $this->Model->where(array('name'=>$name))->find();
		//获取插件的类
        $class          =   get_addon_class($db_addons['name']);
		//设置跳转链接
        $this->assign('jumpUrl',U('index'));
		//如果数据库或者类不存在
        if(!$db_addons || !class_exists($class)){
            $this->error('插件不存在');
		}
		//在session中设置插件的安装错误为空
        session('addons_uninstall_error',null);
		//实例化插件类
        $addons =   new $class;
		//使用前面实例化过的插件类，uninstall操作，且将返回的数据存储在$uninstall_flag中
        $uninstall_flag =   $addons->uninstall();
		//如果$uninstall_flag为false
        if(!$uninstall_flag){
			//返回错误信息，错误数据应该在插件卸载的过程中存储在session中
            $this->error('执行插件预卸载操作失败'.session('addons_uninstall_error'));
        }
		
		//更新钩子上有这个插件的数据
        $hooks_update = D('Hooks')->removeHooks($db_addons['name']);
		//如果更新错误
        if($hooks_update === false){
            $this->error('卸载插件所挂载的钩子数据失败');
        }
		//更新缓存
        S('hooks', null);
		//删除数据库中的记录
		unlinkDir('./Public/Addons/'.$name);
        $delete = $this->Model->where("name='{$db_addons['name']}'")->delete();
        if($delete === false){
            $this->error('卸载插件失败');
        }else{
            $this->success('卸载成功');
        }
    }
}