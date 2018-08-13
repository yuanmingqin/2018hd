<?php

if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

class table_player extends app_table
{
	public function __construct() {

		$this->_table = 'player';

		parent::__construct();
	}
	public function fetch_by_id($id) {
		if(!$id) {
			return false;
		}
		return DB::fetch_first('SELECT * FROM %t WHERE %i', array($this->_table, DB::field('id', $id)));
	}
	public function check_is_valid($id){
		$sql = "SELECT b.start_date,b.end_date FROM ".DB::table('player')." as a left join ".DB::table('group')." as b on a.user_group = b.user_group where a.id  =  '$id'";
		$res = DB::query($sql);
		$result = '';
		while($row = DB::fetch($res)){			
			$result = $row;
		}
		$start_time = strtotime($result['start_date']);
		$end_time = strtotime($result['end_date']);
		if($start_time < time() && time() < $end_time){
			return true;
		}
		return false;
	}
	public function fetch_player_list($pageindex,$pagesize,$user_group,$openid,$keywords) {
		global $_G;
		$openid = $_COOKIE['openid'];
		$result = array();
		$sql = "SELECT a.*,b.group_name FROM ".DB::table('player')." as a left join ".DB::table('group')." as b on a.user_group = b.user_group where 1=1 ";
		$tablearr = array($this->_table);
		if($keywords){
			$sql .=" AND a.name like '%$keywords%'";
		}else{
			if($user_group){
				$sql .=" AND a.user_group = '$user_group'";
			}
		}
		$result['total'] = DB::num_rows(DB::query($sql));
		$result['pageCount'] = ceil($result['total']/$pagesize);
		$sql .= " order by xuhao ASC";
		$start = ($pageindex-1)*$pagesize;
		$sql .= DB::limit($start, $pagesize);
		$res = DB::query($sql);
		$list = array();
		$i = 1;
		while($row = DB::fetch($res)){
			$isVote = C::t('vote')->check_is_vote($openid,$row['id']);
			if($isVote){
				$row['isVote'] = true;
			}else{
				$row['isVote'] = false;
			}
			$player_no = $start+$i;
			$i++;	
			if($player_no < 10){
				$player_no = "0".$player_no;
			}
			$row['player_no']  = $player_no;				
			$list[] = $row;
		}
		$result['res'] = $list;
		return $result;
	}
	//投票
	public function add_poll_num($id){
		$id =intval($id);
		$set = "poll_num = poll_num + 1 ";
		return DB::query("UPDATE %t SET $set WHERE %i LIMIT 1",array($this->_table, DB::field('id', $id)));
					
	}
	public function fetch_filter_csv($mobile) {
		global $_G;
		$sql = "SELECT * FROM ".DB::table('user')." where 1=1 ";
		$tablearr = array($this->_table);
		if($mobile){
			$sql .= ' AND mobile NOT IN ('.dimplode($mobile).')';
		}
		$sql .= " order by id ASC";
		$res = DB::query($sql);
		$list = array();
		while($row = DB::fetch($res)){
			//if(!in_array($row['mobile'],$mobile)){
				$row['img1'] = $_G['imgroot'].$row['img1'];
				$row['img2'] = $_G['imgroot'].$row['img2'];
				$row['op_time'] =$row['op_time']?date("Y-m-d H:i:s",$row['op_time']):'';
				$list[] = $row;
			//}
		}
		return $list;
	}
	public function get_canyu_count(){	
		$count = DB::result_first('SELECT sum(poll_num) FROM %t WHERE 1=1', array($this->_table));
		return $count;
	}
	public function fetch_search_csv($contact,$sr) {
		global $_G;
		$sql = "SELECT * FROM ".DB::table('user')." where 1=1 ";
		$tablearr = array($this->_table);
		if($contact){
			$sql .=" AND contact like '%$contact%'";
		}
		if($sr){
			$sql .=" AND sr like '%$sr%'";
		}
		$sql .= " order by id ASC";
		$res = DB::query($sql);
		$list = array();
		while($row = DB::fetch($res)){
			$row['sex'] = ($row['sex'] == 1)?"男":"女";
			$row['op_time'] =$row['op_time']?date("Y-m-d H:i:s",$row['op_time']):'';
			$list[] = $row;
		}
		return $list;
	}
	public function insert($data){

		return DB::insert($this->_table, $data,true);
	}
		// 修改
	public function update($data, $condition, $unbuffered = false, $low_priority = false){
		return DB::update($this->_table, $data, $condition, $unbuffered, $low_priority);
	}
	public function del_by_id($id){
		if(!$id){
			return false;
		}
		return DB::delete($this->_table,DB::field('id', $id));
	}
}

?>