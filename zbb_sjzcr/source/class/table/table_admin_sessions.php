<?php
/**
 *      admin_sessions表操作对象
 */

if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

class table_admin_sessions extends app_table
{
	public function __construct() {

		$this->_table = 'admin_sessions';
		$this->_pk    = 'sesskey';

		parent::__construct();
	}

	public function fetch_by_key($sesskey) {
		if(!$sesskey) {
			return null;
		}
		return DB::fetch_first('SELECT * FROM %t WHERE %i', array($this->_table, DB::field('sesskey', $sesskey)));
	}

	public function del_by_key($sesskey){
		return DB::delete($this->_table, DB::field('sesskey', $sesskey));
	}
}

?>