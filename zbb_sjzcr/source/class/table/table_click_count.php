<?php
/**
 *    
 *    click_count表操作对象
 */

if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

class table_click_count extends app_table
{
	public function __construct() {

		$this->_table = 'click_count';
		$this->_pk    = 'id';

		parent::__construct();
	}
	public function fetch_group() {
		$res = DB::fetch_all('SELECT huodong_id,SUM(clickcount) as clickcount FROM  %t group by huodong_id',array($this->_table));
		return $res;
	}
	public function fetch_count(){
		$count = DB::result_first('SELECT sum(clickcount) as clickcount FROM  %t where 1 = 1',array($this->_table));
		$canyu_count = C::t("player")->get_canyu_count();
		$count = $count*21+random(2,1)+$canyu_count;
		return $count;
	}
	public function fetch_list($start,$perpage) {
		$res =array();
		$res['result'] = DB::fetch_all('SELECT * FROM %t ORDER BY clicktime DESC %i', array($this->_table,DB::limit($start,$perpage)));
		$res['num'] = DB::result_first('SELECT count(*) FROM %t', array($this->_table));
		return $res;
	}
	public function add_count($array){
		$clicktime = $array['clicktime'];
		$id = DB::result_first('SELECT id FROM %t WHERE clicktime = %s', array($this->_table,$clicktime));
		if($id){
			$id =intval($id);
			$set = "clickcount = clickcount + 1 ";
			return DB::query("UPDATE %t SET $set WHERE %i LIMIT 1",array($this->_table, DB::field('id', $id)));		
		}else{
			$data = array_merge($array,array('clickcount'=>1));
			return $this->insert($data);
		}
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