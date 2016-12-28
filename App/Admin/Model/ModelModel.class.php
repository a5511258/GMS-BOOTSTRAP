<?php

/*
 * 模型模型
 * Auth : Ghj
 * Time : 2015-07-26
 * QQ : 912524639
 * Email : 912524639@qq.com
 * Site : http://guanblog.sinaapp.com/
 */
namespace Admin\Model;

use Think\Model;

class ModelModel extends Model {
	
	/*
	 * 自动验证规则
	 * array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
	 * 验证条件 （可选）
	 * self::EXISTS_VALIDATE 或者0 存在字段就验证（默认）
	 * self::MUST_VALIDATE 或者1 必须验证
	 * self::VALUE_VALIDATE或者2 值不为空的时候验证
	 * 验证时间（可选）
	 * self::MODEL_INSERT或者1新增数据时候验证
	 * self::MODEL_UPDATE或者2编辑数据时候验证
	 * self::MODEL_BOTH或者3全部情况下验证（默认）
	 */
	protected $_validate = array (
			array (
					'name',
					'require',
					'标识不能为空',
					self::MUST_VALIDATE,
					'regex',
					self::MODEL_INSERT 
			),
			array (
					'name',
					'/^[a-zA-Z]\w{0,39}$/',
					'标识不合法',
					self::VALUE_VALIDATE,
					'regex',
					self::MODEL_BOTH 
			),
			array (
					'name',
					'',
					'标识已经存在',
					self::VALUE_VALIDATE,
					'unique',
					self::MODEL_BOTH 
			),
			array (
					'table_name',
					'require',
					'表名不能为空',
					self::VALUE_VALIDATE,
					'regex',
					self::MODEL_BOTH 
			),
			array (
					'table_name',
					'/^[a-zA-Z]\w{0,39}$/',
					'表名不合法',
					self::VALUE_VALIDATE,
					'regex',
					self::MODEL_BOTH 
			),
			array (
					'table_name',
					'',
					'表名已经存在',
					self::VALUE_VALIDATE,
					'unique',
					self::MODEL_BOTH 
			),
			array (
					'title',
					'require',
					'名称不能为空',
					self::MUST_VALIDATE,
					'regex',
					self::MODEL_BOTH 
			),
			array (
					'title',
					'1,30',
					'名称长度不能超过30个字符',
					self::MUST_VALIDATE,
					'length',
					self::MODEL_BOTH 
			) 
	);
	
	/*
	 * 自动验证规则
	 * array(完成字段1,完成规则,[完成条件,附加规则])
	 * 验证时间（可选）
	 * self::MODEL_INSERT或者1	新增数据的时候处理（默认）
	 * self::MODEL_UPDATE或者2	更新数据的时候处理
	 * self::MODEL_BOTH或者3	所有情况都进行处理
	 */
	protected $_auto = array (
			array (
					'create_time',
					NOW_TIME,
					self::MODEL_INSERT 
			),
			array (
					'update_time',
					NOW_TIME,
					self::MODEL_BOTH 
			),
			array (
					'status',
					'1',
					self::MODEL_INSERT,
					'string' 
			) 
	);
	
	/**
	 * 更新模型
	 */
	public function update($_post) {
		$data = $this->create ( $_post );
		if (empty ( $data )) {
			return false;
		}
		
		if (empty ( $data ['id'] )) { // 是否存在模型ID。如果存在就是更新模型，如果不存在就是新增模型
			$id = $this->add ();
			if (! $id) {
				$this->error = '新增模型出错！';
				return false;
			}
			action_log('Add_Model', 'Model', $id);
		} else {
			$status = $this->save ();
			if (false === $status) {
				$this->error = '更新模型出错！';
				return false;
			}
			action_log('Edit_Model', 'Model', $_post['id']);
		}
		return $data;
	}
	
	/**
	 * 获取指定数据库的所有表名
	 */
	public function getTables() {
		return $this->db->getTables ();
	}
	
	/**
	 * 系统化表
	 */
	public function generate($table, $name = '', $title = '') {
		if (empty ( $name )) {
			$name = substr ( $table, strlen ( C ( 'DB_PREFIX' ) ) );
		}
		if (empty ( $title )) {
			$title = substr ( $table, strlen ( C ( 'DB_PREFIX' ) ) );
		}
		$data = array (
				'name' => $name,
				'title' => $title,
				'table_name' => substr ( $table, strlen ( C ( 'DB_PREFIX' ) ) ) 
		);
		$data = $this->create ( $data );
		if ($data) {
			// 新增模型数据
			$res = $this->add ( $data );
			if (! $res) {
				$this->error = $this->getError ();
				return false;
			}
			action_log('Generate_Model', 'Model', $res);
		} else {
			$this->error = $this->getError ();
			return false;
		}
		
		// 新增字段数据
		$fields = M ()->query ( 'SHOW FULL COLUMNS FROM ' . $table );
		$i=1;
		foreach ( $fields as $key => $value ) {
			$value = array_change_key_case ( $value );
			// 不新增id字段
			if (strcmp ( $value ['field'], 'id' ) == 0) {
				continue;
			}
			
			// 生成属性数据
			$data = array ();
			$data ['name'] = $value ['field'];
			$data ['title'] = $value ['comment'];
			$data ['type'] = 'string'; // TODO:根据字段定义生成合适的数据类型
			$data ['extra'] = 'a:1:{s:8:"required";s:1:"0";}'; // string字符串的默认配置
			$is_null = strcmp ( $value ['null'], 'NO' ) == 0 ? ' NOT NULL ' : ' NULL ';
			$data ['field'] = $value ['type'] . $is_null;
			$data ['value'] = $value ['default'] == null ? '' : $value ['default'];
            $data['sort_l'] = $i;
            $data['sort_s'] = $i;
            $data['sort_a'] = $i;
            $data['sort_e'] = $i;
			$data ['model_id'] = $res;
			$_POST = $data; // 便于自动验证
			$i++;
			D ( 'ModelField' )->update ( $data, false );
		}
		return $res;
	}
	
	/**
	 * 删除一个模型
	 */
	public function del($id) {
		// 获取表名
		$model = $this->field ( 'table_name,extend' )->find ( $id );
		$table_name = C ( 'DB_PREFIX' ) . strtolower ( $model ['table_name'] );
		
		// 删除属性数据
		// 删除模型数据
		$this->delete ( $id );
		action_log('Del_Model', 'Model', $id);
		// 删除该表
		$sql = <<<sql
                DROP TABLE {$table_name};
sql;
		M ()->execute ( $sql );
		$this->delete ( $id );
		M ( 'ModelField' )->where ( array ('model_id' => $id ) )->delete ();
		return true;
	}
}
