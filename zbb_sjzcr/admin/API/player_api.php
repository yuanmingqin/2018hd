<?php
require_once '../../source/class/class_core.php';
$kldapp = C::app();
$kldapp->reject_robot();
$kldapp->init();
require_once '../includes/common.php';
$action = $_REQUEST['ac'] ? $_REQUEST['ac'] : '';
$return = array('errcode'=>'0','errmsg'=>'');
if($action == 'del') {
	$id = intval($_GET['id']);
	if(!$id){
		die_return("-1","参数错误");
	}
	C::t("player")->del_by_id($id);
}else{
	$return['errcode'] = -404;
	$return['errmsg'] = 'error action';
}
echo json_encode($return);
exit();