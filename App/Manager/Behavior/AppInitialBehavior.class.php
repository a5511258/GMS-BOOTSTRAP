<?php
/**
 * Created by PhpStorm.
 * User: xhp
 * Date: 2016/4/27
 * Time: 17:15
 */
namespace Manager\Behavior;


use Think\Behavior;

class AppInitialBehavior extends Behavior
{

	public function run(&$params)
	{
		if(isset($_GET["sessionId"])){
            session_id($_GET["sessionId"]);
		}
	}
}