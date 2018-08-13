<?php
/**
 *    秘密信息
 *    
 *    管理员
 */

if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

$perm_list = $config['menu'];
/*------------------------------------------------------ */
//-- 管理员列表  &op=list
/*------------------------------------------------------ */
$operation = $_GET['op'] ? $_GET['op'] : 'list';
if ($operation == 'list')
{
	admin_priv('member');

    /* 查询数据库中管理员列表 */
	$memberlist = C::t("admin_user")->fetchlist();
	foreach($memberlist as $key=>$member) {
		$memberlist[$key]['last_login'] = date('Y-m-d H:i:s', $member['last_login']);
		$memberlist[$key]['add_time'] = date('Y-m-d H:i:s', $member['add_time']);
	}
    $smarty->assign('memberlist', $memberlist);
}
elseif($operation == 'edituser'){
	$login_todo = admin_priv('member');
	$admin_id=$_GET['id'];
	
	/* 查询数据库中对应id管理员 */
	$member = C::t("admin_user")->fetch_by_id($admin_id);
	if($member){
		//非admin不允许操作带权限管理的用户
		if($_COOKIE['cutv_uid'] != 1) {
			if(in_array('member', unserialize($member['todolist']))){
				sys_msg('您没有权限执行该操作');
			}
		}
		
		$member['todolist'] = unserialize($member['todolist']);
		$permlist = array();
		$checkbox_str = check_manage_todo($perm_list, $member, $login_todo);
		$smarty->assign('member', $member);
		$smarty->assign('checkbox_str', $checkbox_str);
	}
}elseif($operation == 'add'){
	$checkbox_str = check_manage_todo($perm_list, null, $login_todo);
	$smarty->assign('checkbox_str', $checkbox_str);
}elseif($operation == 'editpass'){
	checkadminlogin();
	//$_GET['id'] = $_COOKIE['cutv_uid'];
	$admin_id=intval($_COOKIE['cutv_uid']);
	/* 查询数据库中对应id管理员 */
	$member = C::t("admin_user")->fetch_by_id($admin_id);
	$smarty->assign('member', $member);
}