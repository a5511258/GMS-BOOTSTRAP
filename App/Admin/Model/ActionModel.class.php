<?php 
/*
 * 行为模型
 * Auth   : Ghj
 * Time   : 1452659082 
 * QQ     : 912524639
 * Email  : 912524639@qq.com
 * Site   : http://guanblog.sinaapp.com/
 */
 
namespace Admin\Model;
use Think\Model;

class ActionModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'action'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("update_time","time",1,"function"),
     
	);

	public function cache(){
		S('action_list',null);
		$action_list=$this->getField('id,name,title,remark,rule,log,type,status');
		S('action_list',$action_list);
	}
}