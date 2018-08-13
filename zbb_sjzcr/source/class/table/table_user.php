<?php

if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

class table_user extends app_table
{
	public function __construct() {

		$this->_table = 'user';

		parent::__construct();
	}
	public function fetch_user_list($pageindex,$pagesize) {
		global $_G;
		
		$result = array();
		$sql = "SELECT * FROM ".DB::table('user')." where 1 = 1";
		$tablearr = array($this->_table);
		$result['total'] = DB::num_rows(DB::query($sql));
		$result['totalpages'] = ceil($result['total']/$pagesize);
		$sql .= " order by zan DESC";
		$start = ($pageindex-1)*$pagesize;
		$sql .= DB::limit($start, $pagesize);
		$res = DB::query($sql);
		$list = array();
		while($row = DB::fetch($res)){
			$list[] = $row;
		}
		$result['res'] = $list;
		return $result;
	}
	public function fetch_by_flag($openid) {
		return DB::fetch_first('SELECT * FROM %t WHERE openid = %s', array($this->_table, $openid));
	}
	public function add_zan_num($openid){
		$openid =trim($openid);
		$set = "zan = zan + 1 ";
		return DB::query("UPDATE %t SET $set WHERE %i LIMIT 1",array($this->_table, DB::field('openid', $openid)));
		 		
	}
	public function fetch_search_csv() {
		global $_G;
		$sql = "SELECT * FROM ".DB::table('user')." where 1=1 ";
		$tablearr = array($this->_table);
		$sql .= "order by zan DESC";
		$res = DB::query($sql);
		$list = array();
		while($row = DB::fetch($res)){
			//$row['img1'] = $_G['imgroot'].$row['img1'];
			//$row['img2'] = $_G['imgroot'].$row['img2'];
			//$row['op_time'] =$row['op_time']?date("Y-m-d H:i:s",$row['op_time']):'';
			$list[] = $row;
		}
		return $list;
	}
	public function insert($data){
		$info = $this->fetch_by_flag($data['openid']);
		if(!$info['openid']){
			return DB::insert($this->_table, $data,true);
		}else{
			if(!$info['headimgurl'] || !$info['nickname']){
				$condition = array('openid'=>$data['openid']);
				return $this->update($data,$condition);
			}
		}
	}
	// 修改
	public function update($data, $condition, $unbuffered = false, $low_priority = false){
		return DB::update($this->_table, $data, $condition, $unbuffered, $low_priority);
	}
}

?>