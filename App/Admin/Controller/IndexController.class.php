<?php

namespace Admin\Controller;

/**
 * 后台首页控制器
 */
class IndexController extends AdminCoreController {

    /**
     * 后台首页操作
     */
    public function index(){
		$this->assign ( 'AdminMenu', $this->get_menu());
		$this->display ();
    }
	
    /**
     * 控制台
     */
    public function main(){
		$this->display ();
    }

	
    /**
     * 更新缓存
     */
    public function cache() {
        if (isset($_GET['type'])) {
			//设置Dir类
			$Dir = new \Common\Libs\Dir();
			//设置缓存模型
            $cache = D('Common/Cache');
			//获取当前更新类型
            $type = I('get.type');
			//设置执行超时
            set_time_limit(0);
			//根据更新类型选择执行代码
            switch ($type) {
				//更新站点数据缓存
                case "site":
                    //开始刷新缓存
                    $stop = I('get.stop', 0, 'intval');
					//如果没有stop，则证明正在执行系统定义缓存的更新
                    if (empty($stop)) {
						//防止异常终止代码执行
                        try {
                            //已经清除过的目录
                            $dirList = explode(',', I('get.dir', ''));
                            //删除缓存目录下的文件
							//RUNTIME_PATH 应用运行时目录（默认为 APP_PATH.'Runtime/'）
                            $Dir->del(RUNTIME_PATH);
                            //获取子目录
                            $subdir = glob(RUNTIME_PATH . '*', GLOB_ONLYDIR | GLOB_NOSORT);
							//如果还有目录存在继续执行
                            if (is_array($subdir)) {
                                foreach ($subdir as $path) {
                                    $dirName = str_replace(RUNTIME_PATH, '', $path);
                                    //忽略目录
                                    if (in_array($dirName, array('Cache', 'Logs'))) {
                                        continue;
                                    }
                                    if (in_array($dirName, $dirList)) {
                                        continue;
                                    }
                                    $dirList[] = $dirName;
                                    //删除目录
                                    $Dir->delDir($path);
                                    //防止超时，清理一个从新跳转一次
                                    $this->assign("waitSecond", 1);
                                    $this->success("清理缓存目录[{$dirName}]成功！", U('Index/cache', array('type' => 'site', 'dir' => implode(',', $dirList))));
                                    exit;
                                }
                            }
                            //更新开启其他方式的缓存
                            \Think\Cache::getInstance()->clear();
                        } catch (Exception $exc) {
                            
                        }
                    }
					//执行数据库中的缓存
                    if ($stop) {
                        $modules = $cache->getCacheList();
                        //需要更新的缓存信息
                        $cacheInfo = $modules[$stop - 1];
                        if ($cacheInfo) {
                            if ($cache->runUpdate($cacheInfo) !== false) {
                                $this->assign("waitSecond", 1);
                                $this->success('更新缓存：' . $cacheInfo['name'], U('Index/cache', array('type' => 'site', 'stop' => $stop + 1)));
                                exit;
                            } else {
                                $this->error('缓存[' . $cacheInfo['name'] . ']更新失败！', U('Index/cache', array('type' => 'site', 'stop' => $stop + 1)));
                            }
                        } else {
                            $this->assign("waitSecond", 3);
                            $this->success('缓存更新完毕,请刷新网站！', U('Index/cache'));
                            exit;
                        }
                    }
                    $this->success("即将更新站点缓存！", U('Index/cache', array('type' => 'site', 'stop' => 1)));
                    break;
				//更新站点模板缓存
                case "template":
                    //删除缓存目录下的文件
                    $Dir->del(RUNTIME_PATH);
                    $Dir->delDir(RUNTIME_PATH . "Cache/");
                    $Dir->delDir(RUNTIME_PATH . "Temp/");
                    //更新开启其他方式的缓存
                    \Think\Cache::getInstance()->clear();
                    $this->assign("waitSecond", 3);
                    $this->success("模板缓存清理成功！请刷新网站！", U('Index/cache'));
                    break;
				//清除网站运行日志
                case "logs":
                    $Dir->delDir(RUNTIME_PATH . "Logs/");
                    $this->assign("waitSecond", 3);
                    $this->success("站点日志清理成功！请刷新网站！", U('Index/cache'));
                    break;
				//为选择更新类型
                default:
                    $this->error("请选择清楚缓存类型！");
                    break;
            }
        } else {
            $this->display();
        }
    }
}