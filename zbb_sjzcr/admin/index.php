<?php
/**
 *    秘密信息
 *    
 *    后台管理页面
 */
require_once '../source/class/class_core.php';

$kldapp = C::app();
$kldapp->reject_robot();
$kldapp->init();

$action = $_GET['action'] ? $_GET['action'] : 'index';
$action_arr = array('index','adminlog','member','player','player_br');
if(!in_array($action, $action_arr)) {
	exit('Access Denied');
}

require_once('config.ini.php');
require_once 'includes/common.php';
checkadminlogin();

if($action != 'index'){
	include_once($action.'.php');
}

$smarty->template_dir = CUTV_ROOT.'./admin/templates';
$smarty->cache_dir = CUTV_ROOT.'./temp/caches/admin';
$smarty->compile_dir = CUTV_ROOT.'./temp/compiled/admin';

$menu = C::t("sessions_data")->fetch_by_sesskey($_COOKIE['cutv_sid']);
$smarty->assign('menu_json', $menu['data']);
$smarty->assign('username', $_COOKIE['cutv_username']);
$smarty->assign('operation', $operation);
$smarty->display($action.'.htm');