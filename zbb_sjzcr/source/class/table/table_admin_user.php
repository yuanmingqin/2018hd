<?php
/**
 *    秘密信息
 *    
 *    admin_user表操作对象
 */

if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

class table_admin_user extends app_table
{
	public function __construct() {

		$this->_table = 'admin_user';
		$this->_pk    = 'admin_id';

		parent::__construct();
	}

	public function fetch_by_id($id) {
		if(!$id) {
			return false;
		}
		return DB::fetch_first('SELECT admin_id,user_name,todolist,last_ip,last_login,add_time FROM %t WHERE %i', array($this->_table, DB::field('admin_id', $id)));
	}

	public function fetchlist() {
		return DB::fetch_all('SELECT admin_id,user_name,todolist,last_ip,last_login,add_time FROM %t', array($this->_table));
	}

	public function update_by_id($id,$password,$todolist){
		$dps = random(6);
		$password = $password ? "password = '".md5(md5($password).$dps)."',dpass='".$dps."'," : '';
		return DB::query("UPDATE %t SET $password todolist = %s WHERE %i LIMIT 1",array($this->_table, $todolist, DB::field('admin_id', $id)));
	}
	
	public function editpass_by_id($id,$password){
		$dps = random(6);
		$password = $password ? "password = '".md5(md5($password).$dps)."',dpass='".$dps."'" : '';
		if(empty($password)){
			return false;
		}
		return DB::query("UPDATE %t SET $password WHERE %i LIMIT 1",array($this->_table, DB::field('admin_id', $id)));
	}

	public function add_member($user_name,$password,$todolist,$add_time){
		$dps = random(6);
		return DB::insert($this->_table,array("user_name"=>$user_name,"password"=>md5(md5($password).$dps),"dpass"=>$dps,"todolist"=>$todolist,"add_time"=>$add_time));
	}

	public function del_by_id($id){
		return DB::delete($this->_table,DB::field('admin_id', $id));
	}
}

?>