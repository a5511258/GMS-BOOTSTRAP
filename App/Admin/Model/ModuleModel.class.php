<?php 
/*
 * 模块模型
 * Auth   : Ghj
 * Time   : 1457436512 
 * QQ     : 912524639
 * Email  : 912524639@qq.com
 * Site   : http://guanblog.sinaapp.com/
 */
 
namespace Admin\Model;
use Think\Model;

class ModuleModel extends Model{

    public $configpath;
    public $config;

    function _initialize() {
        parent::_initialize();
        define("MENUID", 9);
    }
	
    /*模型中定义的表*/
	protected $tableName = 'module'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
		array("status",1,1,),
		array("create_time","time",1,"function"),
		array("update_time","time",3,"function"),
	);
	
	
    /**
     * 获取模块列表
     * @param string $addon_dir
     */
    public function getList(){
		$module_dir = './App/';
        $dirs = array_map('basename',glob($module_dir.'*', GLOB_ONLYDIR));
        if($dirs === FALSE || !file_exists($module_dir)){
            $this->error = '模块目录不可读或者不存在';
            return FALSE;
        }
		//设置空变量，作为存储插件信息的变量
		$module_arr=array();
		
		
		foreach($dirs as $module_one){
			//设置当前模块的配置路径
			$config_path = APP_PATH .  $module_one . DIRECTORY_SEPARATOR . 'Install' . DIRECTORY_SEPARATOR . 'Config.inc.php';
			
			//如果配置不存在，则返回错误
			if (!file_exists($config_path)) {
				continue;
			}
			
			//引入配置文件
			require $config_path;
			//设置config信息
			$module_arr[$module_one]=array(
				"name" => $name,
				"title" => $title,
				"description" => $description,
				"author" => $author,
				"author_site" => $author_site,
				"author_email" => $author_email,
				"version" => $version,
				"rely_module" => $rely_module,
				"rely_addons" => $rely_addons,
				"status" => '0',
			);
		}
		
		//设置查询条件,即数据库中中模块表的所有记录都应该在插件文件夹中存在，否则不显示记录
		$where['name']	=	array('in',$dirs);
		//获取所有的已安装的模块
		$list = $this->where($where)->select();
		//对于前面从数据库中获取的插件记录，进行操作
		foreach($list as $module){
			$module_arr[$module['name']] = array_merge($module_arr[$module['name']],$module);
		}
        $module_arr = list_sort_by($module_arr,'status','desc');
        return $module_arr;
    }
	

    //安装
    public function install($info) {
		$this->config=$info;
		//设置全局变量INSTALL为true
        define("INSTALL", true);
        //设置模块的安装目录
        $path = APP_PATH . $this->config['name'] . DIRECTORY_SEPARATOR . 'Install' . DIRECTORY_SEPARATOR;
		//检测模块依赖
		$rely_module=$this->config['rely_module'];
		//循环判断所有的依赖模块
		foreach($rely_module as $rely_module_one){
			if(validate_module($rely_module_one['name'],$rely_module_one['version'])!=9){
				$this->error=$rely_module_one['name'].$rely_module_one['version']."不符合版本要求操作失败！";
				return false;
				exit;
			}
		}
		//循环判断所有的依赖插件
		$rely_addons=$this->config['rely_addons'];
		//循环判断所有的依赖模块
		foreach($rely_addons as $rely_addons_one){
			if(validate_module($rely_addons_one['name'],$rely_addons_one['version'])!=9){
				$this->error=$rely_module_one['name'].$rely_module_one['version']."不符合版本要求操作失败！";
				return false;
				exit;
			}
		}
		
        //判断是否存在安装的sql文件
        if (file_exists($path . 'install.sql')) {
			//获取sql文件的内容
            $sql = file_get_contents($path . 'install.sql');
			//对文件的表前缀替换
            $sql_split = $this->sql_split($sql, C("DB_PREFIX"));
			//实例化一个M
            $db = M('');
			//判断返回的sql文件是否正确
            if (is_array($sql_split)) {
				//循环执行所遇的sql
                foreach ($sql_split as $s) {
                    $db->execute($s);
                }
            }
        }
		
        //判断是否存在扩展文件
        if (file_exists($path . 'Extention.inc.php')) {
			//执行扩展文件
            @include ($path . 'Extention.inc.php');
        }
        //移动样式文件夹
		copyDir('./App/'.$this->config['name'].'/Install/'.$this->config['name'].'','./Public/'.$this->config['name'],true);
		$data=$this->create($info);
		if($data){
			$result = $this->add($data);
			if(!$result){
				$error = $this->getError();
				$this->error=$error ? $error : "操作失败";
				//返回
				return false;
			}
		}else{
			$error = $this->getError();
			$this->error=$error ? $error : "操作失败";
			//返回
			return false;
		}
		//返回
        return true;
    }
	
    //卸载
    public function uninstall($module_info) {
        define("UNINSTALL", true);
		//判断module是否存在
        if (!$module_info['name']) {
			$this->error="模块名称不存在";
			return false;
        }
		//判断module是否已经安装
        if ($module_info['status']!=1) {
			$this->error="模块未安装";
			return false;
        }
        //设置模块的卸载目录
		$path = APP_PATH . $module_info['module'] . DIRECTORY_SEPARATOR . 'Uninstall' . DIRECTORY_SEPARATOR;
        //判断是否存在卸载的sql文件
		if (file_exists($path . 'uninstall.sql')) {
			//获取sql文件的内容
			$sql = file_get_contents($path . 'uninstall.sql');
			//对文件的表前缀替换
			$sql_split = $this->sql_split($sql, C("DB_PREFIX"));
			//实例化一个M
			$db = M('');
			//判断返回的sql文件是否正确
            if (is_array($sql_split)) {
				//循环执行所遇的sql
				foreach ($sql_split as $s) {
					$db->execute($s);
				}
			}
		}
        //判断是否存在扩展文件
        if (file_exists($path . 'Extention.inc.php')) {
			//执行扩展文件
            @include ($path . 'Extention.inc.php');
        }
		unlinkDir('./Public/'.$module_info['name']);
		//更新数据库中的模块记录
		M('Module')->where(array("name" => $module_info['name']))->delete();
		//返回
		return true;
    }
	
    //验证安装
    public function check($module_info) {
		//判断module是否存在
        if (!$module_info['name']) {
			return false;
        }
		$module_info2=$this->where(array('name'=>$module_info['name']))->find();
		//判断module是否已经安装
        if ($module_info2['status']>0) {
			return false;
        }
		//设置当前模块的配置路径
        $this->configpath = APP_PATH .  $module_info['name'] . DIRECTORY_SEPARATOR . 'Install' . DIRECTORY_SEPARATOR . 'Config.inc.php';
		//如果配置不存在，则返回错误
        if (!file_exists($this->configpath)) {
			return false;
        }
		//引入配置文件
        require $this->configpath;
		//设置config信息
        $this->config = array(
			"name" => $name,
			"title" => $title,
			"description" => $description,
			"author" => $author,
			"author_site" => $author_site,
			"author_email" => $author_email,
			"version" => $version,
			"rely_module" => $rely_module,
			"rely_addons" => $rely_addons,
			"status" => '0',
        );
		//返回配置信息
        return $this->config;
    }
	
	
    /**
     * 处理sql语句，执行替换前缀都功能。
     * @param string $sql 原始的sql
     * @param string $tablepre 表前缀
     */
    private function sql_split($sql, $tablepre) {
        if ($tablepre != "gms_")
            $sql = str_replace("gms_", $tablepre, $sql);
        $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
        if ($r_tablepre != $s_tablepre)
            $sql = str_replace($s_tablepre, $r_tablepre, $sql);
        $sql = str_replace("\r", "\n", $sql);
        $ret = array();
        $num = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            $queries = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 != '#' && $str1 != '-')
                    $ret[$num] .= $query;
            }
            $num++;
        }
        return $ret;
    }
	
    //文件夹权限检测
    public function chechmod() {
        //检查模板文件夹是否有可写权限 TEMPLATE_PATH
        $tfile = "_test.txt";
        $fp = @fopen($this->templatePath . $tfile, "w");
        if (!$fp) {
            return false;
        }
        fclose($fp);
        $rs = @unlink($this->templatePath . $tfile);
        if (!$rs) {
            return false;
        }
        return true;
    }
	
	public function cache(){
		S('MA_LIST',null);
		$MA_LIST = $this->where(array('status'=>1,'disabled'=>1))->getField ( 'name',true );
		$MA_LIST[]="Admin";
		S('MA_LIST', $MA_LIST);
	}
}