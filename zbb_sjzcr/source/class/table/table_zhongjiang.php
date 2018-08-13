<?php

if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

class table_zhongjiang extends app_table
{
	public function __construct() {

		$this->_table = 'zhongjiang';

		parent::__construct();
	}
	public function fetch_choujiang_list($pageindex,$pagesize,$iszhong='',$huodong_id='') {
		global $_G;
		
		$result = array();
		$sql = "SELECT a.*,b.name,c.* FROM ".DB::table('zhongjiang')." as a left join ".DB::table('huodong')."   as b  on a.huodong_id = b.id left join ".DB::table('user')." as c on a.openid = c.openid where 1=1 ";
		$tablearr = array($this->_table);
		if($iszhong == 1){
			$sql .=" AND a.prize_name is not null";
		}else if($iszhong == -1){
			$sql .=" AND a.prize_name is null";
		}
		if($huodong_id){
			$sql .=" AND a.huodong_id = $huodong_id";
		}
		$result['total'] = DB::num_rows(DB::query($sql));
		$sql .= " order by a.id ASC";
		$start = ($pageindex-1)*$pagesize;
		$sql .= DB::limit($start, $pagesize);
		$res = DB::query($sql);
		$list = array();
		while($row = DB::fetch($res)){			
			$row['zhongjiang_time'] =$row['zhongjiang_time']?date("Y-m-d H:i:s",$row['zhongjiang_time']):'';
			$list[] = $row;
		}
		$result['res'] = $list;
		return $result;
	}
	public function fetch_my_zhongjiang_list($pageindex,$pagesize,$openid,$huodong_id) {
		global $_G;	
		$result = array();
		$sql = "SELECT * FROM ".DB::table('zhongjiang')." where 1=1 ";
		$tablearr = array($this->_table);
		if($openid){
			$sql .=" AND openid = '$openid'";
		}
		if($huodong_id){
			$sql .=" AND huodong_id = '$huodong_id'";
		}
		$result['total'] = DB::num_rows(DB::query($sql));
		$sql .= " order by zhongjiang_time ASC";
		$start = ($pageindex-1)*$pagesize;
		$sql .= DB::limit($start, $pagesize);
		$res = DB::query($sql);
		$list = array();
		while($row = DB::fetch($res)){			
			$row['zhongjiang_time'] =$row['zhongjiang_time']?date("Y年m月d日",$row['zhongjiang_time']):'';
			$list[] = $row;
		}
		$result['res'] = $list;
		return $result;
	}
	public function fetch_zhongjiang_list($pageindex,$pagesize,$huodong_id='',$phone='') {
		global $_G;
		
		$result = array();
		$sql = "SELECT a.*,b.name,c.openid,c.headimgurl,c.nickname FROM ".DB::table('zhongjiang')." as a left join ".DB::table('huodong')."   as b  on a.huodong_id = b.id left join ".DB::table('user')." as c on a.openid = c.openid where a.mobile is not null ";
		$tablearr = array($this->_table);
		if($huodong_id){
			$sql .=" AND a.huodong_id = $huodong_id";
		}
		if($phone){
			$sql .=" AND a.mobile = '$phone'";
		}
		$result['total'] = DB::num_rows(DB::query($sql));
		$sql .= " order by a.zhongjiang_time ASC";
		$start = ($pageindex-1)*$pagesize;
		$sql .= DB::limit($start, $pagesize);
		$res = DB::query($sql);
		$list = array();
		while($row = DB::fetch($res)){			
			$row['zhongjiang_time'] =$row['zhongjiang_time']?date("Y-m-d H:i:s",$row['zhongjiang_time']):'';
			$list[] = $row;
		}
		$result['res'] = $list;
		return $result;
	}
	public function fetch_filter_csv($iszhong,$huodong_id) {
		global $_G;
		$sql = "SELECT * FROM ".DB::table('zhongjiang')." as a left join ".DB::table('huodong')."   as b  on a.huodong_id = b.id where 1=1 ";
		$tablearr = array($this->_table);
		if($iszhong){
			$sql .=" AND a.prize_name != ''";
		}
		if($huodong_id){
			$sql .=" AND a.huodong_id = $huodong_id";
		}
		$sql .= " order by a.id ASC";
		$res = DB::query($sql);
		$list = array();
		while($row = DB::fetch($res)){
				$row['zhongjiang_time'] =$row['zhongjiang_time']?date("Y-m-d H:i:s",$row['zhongjiang_time']):'';
				$list[] = $row;
		}
		return $list;
	}
	public function fetch_zhongjiang_filter_csv($iszhong,$huodong_id,$phone) {
		global $_G;
		$sql = "SELECT * FROM ".DB::table('zhongjiang')." as a left join ".DB::table('huodong')."   as b  on a.huodong_id = b.id where a.mobile is not null ";
		$tablearr = array($this->_table);
		if($iszhong){
			$sql .=" AND a.prize_name != ''";
		}
		if($phone){
			$sql .=" AND a.phone = $phone";
		}
		if($huodong_id){
			$sql .=" AND a.huodong_id = $huodong_id";
		}
		$sql .= " order by a.id ASC";
		$res = DB::query($sql);
		$list = array();
		while($row = DB::fetch($res)){
				$row['zhongjiang_time'] =$row['zhongjiang_time']?date("Y-m-d H:i:s",$row['zhongjiang_time']):'';
				$row['is_lingqu'] = ($row['is_lingqu'] == -1)?'未领取':'已领取';
				$list[] = $row;
		}
		return $list;
	}
	public function fetch_zhongjiang($mobile) {
		if(!$mobile) {
			return false;
		}
		$res = DB::fetch_all("SELECT * FROM %t WHERE mobile = %s and prize_name != ''", array($this->_table, $mobile));
		$list = array();
		foreach($res as $v){
			$v['zhongjiang_time'] = date('Y-m-d H:i:s',$v['zhongjiang_time']);
			$list[] = $v;
		}
		return $list;
	}
	public function check_is_zhongjiang($openid,$id) {
		if(!$openid || !$id) {
			return false;
		}
		$id = intval($id);
		$count =  DB::fetch_first("SELECT count(*) as count FROM ".DB::table('zhongjiang') ." WHERE  openid = '$openid' and huodong_id = $id and type=2 and prize_name is not null" );
		if(intval($count['count'])>=1){
			return true;
		}
		return false;
	}
	public function check_is_chou($openid,$date,$id) {
		if(!$openid || !$date || !$id) {
			return false;
		}
		$id = intval($id);
		$count =  DB::fetch_first("SELECT count(*) as count FROM ".DB::table('zhongjiang') ." WHERE FROM_UNIXTIME(zhongjiang_time,'%Y-%m-%d') = '$date' and openid = '$openid' and huodong_id = $id and type=2" );
		return intval($count['count']);
	}
	public function tongGuo($ids){
		$set = "is_lingqu = 1 ";
		return DB::query("UPDATE %t SET $set WHERE %i",array($this->_table, DB::field('id', $ids)));
	}
	public function buTongGuo($ids){
		$set = "is_lingqu = -1 ";
		return DB::query("UPDATE %t SET $set WHERE %i",array($this->_table, DB::field('id', $ids)));
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