<?php
require_once '../../source/class/class_core.php';
$kldapp = C::app();
$kldapp->reject_robot();
$kldapp->init();
require_once('../config.ini.php');
require_once '../includes/common.php';
$action = $_REQUEST['ac'] ? $_REQUEST['ac'] : '';
$return = array('errcode'=>'0','errmsg'=>'');
if($action == 'login'){
	$username=$_POST['admin_username'];
	$password=$_POST['admin_password'];
	$captcha=$_POST['captcha'];
	if(!$username || !$password){
		die_return("-1","用户名或密码不能为空");
	}
	if(!$captcha){
		die_return("-2","请输入验证码");
	}
	/* 检查验证码是否正确 */
	$validator = new app_captcha();
	$validator->session_word = 'captcha_admin';
	if (!$validator->check_word($captcha))
	{
		die_return('-3','验证码输入错误');
	}
	$user = DB::fetch_first("SELECT * FROM ".DB::table('admin_user')." WHERE user_name='$username'");
	if(!$user) {
		die_return('-4','用户名不存在或密码错误');
	} elseif($user['password'] != (empty($user['dpass']) ? md5($password) : md5(md5($password).$user['dpass']))) {
		die_return('-5','用户名不存在或密码错误');
	}
	DB::query("DELETE FROM ".DB::table('admin_sessions')." WHERE adminid='$user[admin_id]'");
	$logintime = time();
	$sesskey = md5($user['admin_id'].getglobal('clientip').$logintime.random(32));
	$data = array(
		'last_ip' => getglobal('clientip'),
		'last_login' => $logintime,
	);
	DB::update('admin_user', $data, "admin_id='$user[admin_id]'");

	$data = array(
		'sesskey' => $sesskey,
		'expiry' => $logintime+100*60,
		'adminid' => $user['admin_id'],
		'ip' => getglobal('clientip'),
		'user_name' => $user['user_name'],
		'data' => $user['todolist'],
	);
	DB::insert('admin_sessions', $data, false, true);
	
	//加载菜单
	$perm_list = $config['menu'];
	if($user['admin_id'] != 1){
		check_priv($perm_list, unserialize($user['todolist'])); //删除没有的权限项
		array_empty_filter($perm_list); //清空没有权限的菜单
	}
	C::t("sessions_data")->replace($sesskey, (time()+3600), json_encode($perm_list));
	
	mySetCookie('cutv_uid', $user['admin_id']);
	mySetCookie('cutv_username', $user['user_name']);
	mySetCookie('cutv_sid', $sesskey);
	mySetCookie('cutv_tid', $logintime);
	
	//写入日志
	admin_log("管理员登录,name:".$user['user_name'].",ID:".$user['admin_id'], $user['admin_id']);
	//清除旧数据
	DB::query("DELETE FROM ".DB::table('sessions_data')." WHERE expiry<=".(time()-48*60*60));  //48小时前
	DB::query("DELETE FROM ".DB::table('admin_sessions')." WHERE expiry<=".(time()-48*60*60)); //48小时前
	DB::query("DELETE FROM ".DB::table('admin_log')." WHERE log_time<=".(time()-12*30*24*60*60)); //12个月前
		
}else{
	$return['errcode'] = -404;
	$return['errmsg'] = 'error action';
}
echo json_encode($return);
exit();