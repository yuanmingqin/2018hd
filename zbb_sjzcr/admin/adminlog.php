<?php
/**
 *    秘密信息
 *    
 *    后台日志管理
 */
if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

$operation = $_GET['op'] ? $_GET['op'] : 'list';

if ($operation == 'list') {
	admin_priv('adminlog');

	
	$curpage = (int)$_GET['page'] ? $_GET['page'] : 1;
	$perpage = 10;
	$start = ($curpage - 1) * $perpage;
	
	$where = '1';
	$where .= $_GET['adminlist'] ? " AND a.adminid='$_GET[adminlist]' " : '';
	$where .= $_GET['key'] ? " AND a.log_info like '%$_GET[key]%' " : '';
	
	$sql = "SELECT a.*, b.user_name FROM " . DB::table('admin_log'). " AS a " .
           "LEFT JOIN " . DB::table('admin_user') . " AS b ON b.admin_id =  a.adminid" . " WHERE $where ORDER BY log_time DESC LIMIT $start,$perpage";
	
	$res = DB::query($sql);
	$log_list = array();
	while ($row = DB::fetch($res)) {
		$row['log_time'] = date('Y-m-d H:i:s', $row['log_time']);
		$log_list[] = $row;
	}
	
	$admin_list = DB::fetch_all("SELECT admin_id,user_name FROM " . DB::table('admin_user'));
	$querytring = http_build_query(array(
		'adminlist' => $_GET['adminlist'],
		'key' => $_GET['key']
	));
	$num = DB::result_first("SELECT count(*) FROM " . DB::table('admin_log'). " AS a " .
           "LEFT JOIN " . DB::table('admin_user') . " AS b ON b.admin_id =  a.adminid" . " WHERE $where");
	$multi = multi($num, $perpage, $curpage, 'index.php?action=adminlog&op=list&'.$querytring);
	
	$smarty->assign('multi', $multi);
	$smarty->assign('admin_list', $admin_list);
	$smarty->assign('log_list', $log_list);
	
}