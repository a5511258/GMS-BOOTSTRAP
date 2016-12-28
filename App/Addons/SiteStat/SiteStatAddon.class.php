<?php
namespace Addons\SiteStat;

use Common\Controller\Addon;

/**
 * 系统环境信息插件
 * @author thinkphp
 */
class SiteStatAddon extends Addon{


    public function __construct(){
		//继承初始化方法
		parent::__construct ();
        $this->assign('ADDON_PATH',__ROOT__.'/Public/Addons/SiteStat/');
	}
	
    public $info = array(
        'name'=>'SiteStat',
        'title'=>'站点统计信息',
        'description'=>'统计站点的基础信息',
        'version'=>'0.1',
        'author'=>'管侯杰',
        'author_site'=>'http://guanblog.sinaapp.com/',
        'author_email'=>'912524639@qq.com',
		'is_config'=>0,
		'config'=>'',
		'rely_module'=>array(),
		'rely_addons'=>array(),
    );

    public function install(){
        return true;
    }

    public function uninstall(){
        return true;
    }

    //实现的AdminIndex钩子方法
    public function AdminIndex($param){
		
        //服务器信息
        $info = array(
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式' => php_sapi_name(),
            'MYSQL版本' => mysql_get_server_info(),
            '产品名称' => '<font color="#FF0000">'.C('SOFT_NAME').'</font>' . "&nbsp;&nbsp;&nbsp; [<a href='".C('SOFT_SITE')."' target='_blank'>访问官网</a>]",
            '产品版本' => '<font color="#FF0000">'.C('SOFT_VERSION').'</font>',
            '产品流水号' => '<font color="#FF0000">20160124</font>',
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time') . "秒",
            '剩余空间' => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
        );
        $this->assign('server_info', $info);
		$this->display('info');
    }
}