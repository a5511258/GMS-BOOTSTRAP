<?php 
/*
 * 云商店
 * Auth   : Ghj
 * Time   : 2016年01月09日 
 * QQ     : 912524639
 * Email  : 912524639@qq.com
 * Site   : http://guanblog.sinaapp.com/
 */
 
namespace Admin\Controller;

class CloudController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
    }
	
    /* 列表(默认首页)
     * Auth   : Ghj
     * Time   : 2016年01月09日 
     **/
	public function index(){
		if (IS_POST) {
			$post_data = I ( 'post.' );
			$map = array ();
			$data = file_get_contents(C('CLOUD_SHOP_SITE').'&type='.I('get.type',1).'&page='.$post_data ['page']);
			echo $data;
		} else {
			$this->display ();
		}
	}
	
    //云端模块下载安装
    public function install() {
		$this->error('正在开发中，暂时无法使用！');
		//
        $id = I('get.id',0);
        if (empty($id)) {
            $this->error('请选择需要安装的模块！');
        }
        $this->assign('stepUrl', U('public_step_1', array('sign' => $sign)));
        $this->assign('sign', $sign);
        $this->display();
    }
}