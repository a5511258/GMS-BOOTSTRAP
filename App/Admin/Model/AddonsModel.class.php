<?php

namespace Admin\Model;
use Think\Model;

class AddonsModel extends Model {

    /**
     * 查找后置操作
     */
    protected function _after_find(&$result,$options) {

    }

    protected function _after_select(&$result,$options){

        foreach($result as &$record){
            $this->_after_find($record,$options);
        }
    }
    /**
     * 文件模型自动完成
     * @var array
     */
    protected $_auto = array(
    	array("create_time","strtotime",0,"function"),
    	array("update_time","strtotime",3,"function"),
    );

    /**
     * 获取插件列表
     * @param string $addon_dir
     */
    public function getList($addon_dir = ''){
		//如果没有插件文件夹参数传递过来，使用APP_ADDON_PATH变量作为插件的路径
        if(!$addon_dir){
            $addon_dir = APP_ADDON_PATH;
		}
		//使用glob函数获取$addon_dir目录下所有的文件名，并且通过array_map方法对返回数组的每个值返回文件名称
        $dirs = array_map('basename',glob($addon_dir.'*', GLOB_ONLYDIR));
        if($dirs === FALSE || !file_exists($addon_dir)){
            $this->error = '插件目录不可读或者不存在';
            return FALSE;
        }
		//设置空变量，作为存储插件信息的变量
		$addon_arr=array();
		
		
		foreach($dirs as $addon_one){
			//获取当前插件的入口类
			$class = get_addon_class($addon_one);
			//如果不存在此类
			if(!class_exists($class)){
				//如果实例化失败，记录到日志文件中去 
				\Think\Log::record('插件'.$addon_one.'的入口文件不存在！');
				//结束本次循环
				continue;
			}
			//实例化当前类
            $obj    =   new $class;
			//在插件数组中这是当前插件的信息，即实例化类中的配置信息
			$addon_arr[$addon_one]	= $obj->info;
			$addon_arr[$addon_one]['status']= 0;
		}
		
		//设置查询条件,即数据库中中模块表的所有记录都应该在插件文件夹中存在，否则不显示记录
		$where['name']	=	array('in',$dirs);
		//获取所有的已安装的模块
		$list = $this->where($where)->select();
		//对于前面从数据库中获取的插件记录，进行操作
		foreach($list as $addon_s){
			$addon_arr[$addon_s['name']] = array_merge($addon_arr[$addon_s['name']],$addon_s);
		}
        $addon_arr = list_sort_by($addon_arr,'status','desc');
        return $addon_arr;
    }

}
