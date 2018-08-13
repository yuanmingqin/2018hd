<?php
/**
 *    秘密信息
 *    
 *   sysdata表操作对象
 */
if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

class table_sysdata extends app_table
{
	public function __construct() {

		$this->_table = 'sysdata';
		$this->_pk    = 'id';

		parent::__construct();
	}
	
	// 获取系统数据
	public function get_data($key_name) {
		$sql = "SELECT key_value,remark,update_time FROM %t where 1=1 ";
		$tablearr = array($this->_table);
		if($key_name){
			$sql .=" AND key_name='".$key_name."'";
		}
		
		$res = DB::fetch_first($sql,$tablearr);
		
		return $res;
	}
	
	// 更新系统数据
	public function update_data($key_name, $key_value, $remark, $update_time) {
		$res = DB::fetch_first("SELECT key_name FROM %t WHERE key_name=%s", array($this->_table, $key_name));
		
		if($res && $res['key_name']){
			DB::update($this->_table, array(
				"key_value" => $key_value,
				"remark" => $remark,
				"update_time" => $update_time
			),array(
				"key_name" => $key_name
			));
		}
		else{
			DB::insert($this->_table, array(
				"key_name" => $key_name,
				"key_value" => $key_value,
				"remark" => $remark,
				"update_time" => $update_time
			));
		}
	}
	public function del_by_key($key_name){
		return DB::delete($this->_table, DB::field('key_name', $key_name));
	}
}	
?>