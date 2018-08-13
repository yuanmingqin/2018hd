<?php
/**
 *    秘密信息
 *    sessions_data表操作对象
 */

if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

class table_sessions_data extends app_table
{
	public function __construct() {

		$this->_table = 'sessions_data';
		$this->_pk    = 'sesskey';

		parent::__construct();
	}
	
	public function fetch_by_sesskey($sesskey) {
		if(!$sesskey) {
			return false;
		}
		return DB::fetch_first('SELECT * FROM %t WHERE %i', array($this->_table, DB::field('sesskey', $sesskey)));
	}

	// 替换插入
	public function replace($sesskey, $expiry, $data) {
		return DB::query("REPLACE INTO %t (sesskey, expiry, data) VALUES(%s, %d, %s)",array($this->_table, $sesskey, $expiry, $data));
	}
	
	// 新增
	public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		return DB::insert($this->_table, $data, $return_insert_id, $replace, $silent);
	}
	
	// 修改
	public function update($data, $condition, $unbuffered = false, $low_priority = false){
		return DB::update($this->_table, $data, $condition, $unbuffered, $low_priority);
	}
}

?>