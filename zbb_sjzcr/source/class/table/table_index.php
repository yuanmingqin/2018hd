<?php

if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

class table_index extends app_table
{
	public function __construct() {

		$this->_table = 'index';

		parent::__construct();
	}

	public function fetch_by_flag($flag) {
		if(!$flag) {
			return false;
		}
		return DB::fetch_first('SELECT * FROM %t WHERE %i', array($this->_table, DB::field('flag', $flag)));
	}
	public function update($data){

		return DB::update($this->_table, $data);
	}
}

?>