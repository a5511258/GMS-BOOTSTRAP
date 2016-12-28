<?php 
/*
 * 模块控制器
 * Auth   : Ghj
 * Time   : 2016年03月08日 
 * QQ     : 912524639
 * Email  : 912524639@qq.com
 * Site   : http://guanblog.sinaapp.com/
 */
 
namespace Admin\Controller;

class ModuleController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('Module');
    }
	
    /* 列表(默认首页)
     * Auth   : Ghj
     * Time   : 2016年03月08日 
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
     * 模块安装 
     */
    public function install() {
        $name = I('param.modulename','');
		if($name==''){
			$this->error('模块名称不能为空');
		}
		
		//设置当前模块的配置路径
		$config_path = APP_PATH .  $name . DIRECTORY_SEPARATOR . 'Install' . DIRECTORY_SEPARATOR . 'Config.inc.php';
		
		//如果配置不存在，则返回错误
		if (!file_exists($config_path)) {
			continue;
		}
		
		//引入配置文件
		require $config_path;
		
		//设置config信息
		$module_info=array(
			"name" => $name,
			"title" => $title,
			"description" => $description,
			"author" => $author,
			"author_site" => $author_site,
			"author_email" => $author_email,
			"version" => $version,
			"rely_module" => $rely_module,
			"rely_addons" => $rely_addons,
		);
		if(IS_POST){
			$info =  $this->Model->check($module_info);
            if ($this->Model->install($info)) {
				$this->Model->cache();
                $this->success("安装成功，请及时更新缓存！", U("Module/index"));
            } else {
				$error = $this->Model->getError();
                $this->error($error ? $error : "安装失败！");
            }
		}else{
            $this->assign('name',$name);
            $this->assign('info',$module_info);
            $this->display();
		}
    }
	
    /**
     * 模块卸载 
     */
    public function uninstall() {
        $name = I('get.modulename','');
		if($name==''){
			$this->error('模块名称不能为空'.$name);
		}
		//获取卸载模型的信息
		$module_info=$this->Model->where(array('name'=>$name))->find();
		//判断是否存在此模型
        if (!$module_info['name']) {
            $this->error("请选择需要卸载的模块！");
        }
		//执行卸载程序
        if ( $this->Model->uninstall($module_info)) {
			$this->Model->cache();
			//成功后提示
            $this->success("模块卸载成功，请及时更新缓存！", U("Module/index"));
        } else {
			//失败后提示
            $this->error("模块卸载失败！", U("Module/index"));
        }
    }
	
	
    /**
     * 模块启用 禁用
     */
    public function disabled() {
		$name=I('get.modulename');
		$d=I('get.d',1);
		empty($name)&&$this->error('参数不能为空！');
		$res=$this->Model->where(array('name'=>$name))->save(array('disabled'=>$d));
		if(!$res){
			$error=$this->Model->getError();
			$this->error($error?$error:'操作失败');
		}else{
			$this->Model->cache();
			$this->success('更改成功！');
		}
    }
    /**
     * 模块配置
     */
    public function config() {
		if(IS_POST){
			$name=I('post.modulename');
			$post_data=I('post.setting');
			$_data['setting']=serialize($post_data);
			$result = $this->Model->where(array('name'=>$name))->save($_data);
			if($result){
				$this->Model->cache();
				$this->success ( "操作成功！",U('index'));
			}else{
                $error = $this->Model->getError();
                $this->error($error ? $error : "操作失败！");
			}
		}else{
			$_info=I('get.');
			
			$_info = $this->Model->where(array('name'=>$_info['name']))->find();
			$this->assign('_info', $_info);
			
			$setting=unserialize($_info['setting']);
			$this->assign('setting', $setting);
			
        	$this->display($_info['module'].'@Public/config');
		}
    }
}