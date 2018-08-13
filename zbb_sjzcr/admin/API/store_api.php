<?php
require_once '../../source/class/class_core.php';
$kldapp = C::app();
$kldapp->reject_robot();
$kldapp->init();
require_once '../includes/common.php';
$action = $_REQUEST['ac'] ? $_REQUEST['ac'] : '';
$return = array('errcode'=>'0','errmsg'=>'');
if($action == 'add'){
		$business_name=$_POST['business_name'];
		$tag=$_POST['tag'];
		$address=$_POST['address'];
		$contacts=$_POST['contacts'];
		if(empty($business_name) || empty($tag)){
			die_return("-1","输入值不能为空");
		}elseif(DB::result_first("SELECT business_name FROM ".DB::table('store')." WHERE business_name='$business_name'")){
			die_return("-2","店名已经存在");
		}
		$data = array(
			'business_name'=>$business_name,
			'tag'=>$tag,
			'address'=>$address,
			'contacts'=>$contacts
		);
		/* 添加管理员 */
		if(C::t("store")->insert($data)){
			admin_log("添加店名,name:".$business_name);	
		}else{
			die_return("-4","添加失败，请稍后重试！");
		}
}else if($action == 'edit'){
		$business_name=$_POST['business_name'];
		$tag=$_POST['tag'];
		$address=$_POST['address'];
		$contacts=$_POST['contacts'];
		$id= intval($_POST['id']);
		if(empty($business_name) || empty($tag)){
			die_return("-1","输入值不能为空");
		}
		$data = array(
			'business_name'=>$business_name,
			'tag'=>$tag,
			'address'=>$address,
			'contacts'=>$contacts
		);
		$condition = array("id"=>$id);
		C::t("store")->update($data, $condition);
}elseif($action == 'del') {
	$id = intval($_GET['id']);
	if(!$id){
		die_return("-1","参数错误");
	}
	/* 删除数据库中对应id管理员 */
	C::t("store")->del_by_id($id);

	//写入日志
	admin_log("删除店,ID:".$id);
}else{
	$return['errcode'] = -404;
	$return['errmsg'] = 'error action';
}
echo json_encode($return);
exit();