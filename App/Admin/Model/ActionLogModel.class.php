<?php 
/*
 * 行为日志模型
 * Auth   : Ghj
 * Time   : 1452659098 
 * QQ     : 912524639
 * Email  : 912524639@qq.com
 * Site   : http://guanblog.sinaapp.com/
 */
 
namespace Admin\Model;
use Think\Model;

class ActionLogModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = ''; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","time",1,"function"),
     
	);
    
	
}