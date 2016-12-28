<?php
namespace Common\Behavior;
use Think\Hook;
defined('THINK_PATH') or exit();

// 初始化钩子信息
class InitHookBehavior extends \Think\Behavior {

    // 行为扩展的执行入口必须是run
    public function run(&$content){
        if(defined('BIND_MODULE') && BIND_MODULE === 'Install') return;
        
        $data = S('Hooks');
        if(count($data)>0){
            $hooks = M('Hooks')->getField('name,addons');
            foreach ($hooks as $key => $value) {
                if($value){
                    $map['disabled']  =   1;
                    $names          =   explode(',',$value);
                    $map['name']    =   array('IN',$names);
                    $data = M('Addons')->where($map)->getField('id,name');
                    if($data){
                        $addons = array_intersect($names, $data);
                        Hook::add($key,array_map('get_addon_class',$addons));
                    }
                }
            }
            S('Hooks',Hook::get());
        }else{
            Hook::import($data,false);
        }
    }
}