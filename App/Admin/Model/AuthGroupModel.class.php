<?php 
/*
 * 用户组模型
 * Auth   : Ghj
 * Time   : 1452665039 
 * QQ     : 912524639
 * Email  : 912524639@qq.com
 * Site   : http://guanblog.sinaapp.com/
 */
 
namespace Admin\Model;
use Think\Model;

class AuthGroupModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = ''; 

    /* 自动验证规则 */
	protected $_validate = array(
        array('title', 'require', '用户名不能为空！'),
        array('title', '', '帐号名称已经存在！', 0, 'unique', 1),
        array('status', 'require', '角色状态不能为空！'),
        array('status', array(0, 1), '状态错误，状态只能是1或者0！', 2, 'in'),
	);

    /* 自动完成规则 */
	protected $_auto = array(

        array('editable', '1'),
        array('visible', '1'),
        array('comment', '自动添加字段测试')

     
	);

}