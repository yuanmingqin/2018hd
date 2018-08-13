<?php

if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

class table_config extends app_table
{
	public function __construct() {

		$this->_table = 'config';

		parent::__construct();
	}
	public function fetch_by_id($id) {
		if(!$id) {
			return false;
		}
		return DB::fetch_first('SELECT * FROM %t WHERE %i', array($this->_table, DB::field('id', $id)));
	}
	public function fetch_by_huodong_id($huodong_id) {
		if(!$huodong_id) {
			return false;
		}
		return DB::fetch_all('SELECT * FROM %t WHERE %i', array($this->_table, DB::field('huodong_id', $huodong_id)));
	}
	public function del_by_id($id){
		return DB::delete($this->_table,DB::field('id', $id));
	}
	public function insert($data){

		return DB::insert($this->_table, $data,true);
	}
		// 修改
	public function update($data, $condition, $unbuffered = false, $low_priority = false){
		return DB::update($this->_table, $data, $condition, $unbuffered, $low_priority);
	}
}

?>