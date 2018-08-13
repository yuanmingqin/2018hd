<?php
/**
 *    秘密信息
 *    
 *    后台登录页面
 */
require_once '../source/class/class_core.php';
require_once('config.ini.php');
require_once 'includes/common.php';
$kldapp = C::app();
$kldapp->reject_robot();
$kldapp->init();
$action = $_GET['action'] ? $_GET['action'] : 'login';
$action_arr = array('login', 'logout', 'clear_cache', 'captcha');
if(!in_array($action, $action_arr)) {
	$action = 'login';
}
if ($action == 'login')
{	
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    $smarty->assign('random',     mt_rand());
	$smarty->assign('cutv_year',   date("Y"));
}
elseif ($action == 'logout')
{
	try{
		C::t("admin_sessions")->del_by_key($_COOKIE['cutv_sid']);
	}catch(Exception $e){
		
	}
	
	setcookie('cutv_uid', '');
	setcookie('cutv_username', '');
	setcookie('cutv_sid', '');
	setcookie('cutv_tid', '');
	header('location:login.php');
}
else if($action == 'captcha'){
	$width = (isset($_GET['width']) && intval($_GET['width']) > 0) ? intval($_GET['width']) : 292;
	$height = (isset($_GET['height']) && intval($_GET['height']) > 0) ? intval($_GET['height']) : 44;
	
	$img = new app_captcha(CUTV_ROOT . './images/captcha/', $width, $height);
	@ob_end_clean(); //清除之前出现的多余输入
	$img->session_word = 'captcha_admin';
	$img->generate_image();
	exit;
}
else if($action == 'clear_cache'){
	checkadminlogin();
	
	$clearResult = clear_cache();
	
	$link[] = array('text' => '返回主界面', 'href' => 'index.php');
	sys_msg('清除缓存成功<br /><br />'.implode("<br />", $clearResult), 0, $link, false);
}
$smarty->template_dir = CUTV_ROOT.'./admin/templates';
$smarty->cache_dir = CUTV_ROOT.'./temp/caches/admin';
$smarty->compile_dir = CUTV_ROOT.'./temp/compiled/admin';
$smarty->assign('operation', $operation);
$smarty->display($action.'.htm');