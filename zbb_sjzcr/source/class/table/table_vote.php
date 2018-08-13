<?php
/**
 *    CUTV深圳台秘密信息
 */

if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

class table_vote extends app_table
{
	public function __construct() {

		$this->_table = 'vote';
		$this->_pk    = 'id';

		parent::__construct();
	}
	public function fetch_by_id($id) {
		if(!$id) {
			return false;
		}
		return DB::result_first('SELECT id FROM %t WHERE %i', array($this->_table, DB::field('id', $id)));
	}
	public function check_is_exist($openid){
		$date =  date("Y-m-d");
		return DB::result_first('SELECT id FROM %t WHERE openid = %s and date = %s', array($this->_table, $openid,$date));
	}
	public function check_is_vote($openid,$voteid){

		return DB::result_first('SELECT id FROM %t WHERE openid = %s and voteid = %s', array($this->_table, $openid,$voteid));
	}
	public function get_canyu_count(){	
		$count = DB::result_first('SELECT count(*) as count FROM %t WHERE 1=1', array($this->_table));
		//$count = $count*21+random(2,1);
		$count = 39091+$count;
		return $count;
	}
	public function check_is_more($openid,$user_group){
		$date =  date("Y-m-d");
		$times = DB::result_first('SELECT count(*) FROM %t WHERE openid = %s and date = %s and user_group = %d', array($this->_table, $openid,$date,$user_group));
		return $times;
	}
	public function check_is_more_from_ip($ip,$user_group){
		$date =  date("Y-m-d");
		$times = DB::result_first('SELECT count(*) FROM %t WHERE ip = %s and date = %s and user_group = %d', array($this->_table, $ip,$date,$user_group));
		return $times;
	}
	public function check_is_more_from_imei($imei,$user_group){
		$date =  date("Y-m-d");
		$times = DB::result_first('SELECT count(*) FROM %t WHERE imei = %s and date = %s and user_group = %d', array($this->_table, $imei,$date,$user_group));
		return $times;
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