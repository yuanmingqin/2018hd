<?php
/**
 *    秘密信息
 *    
 *    管理员
 */

if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}
$user_GROUP = $config['user_group'];
include_once(CUTV_ROOT . '/source/function/function_ftp.php');
$image = new app_img();
$operation = $_GET['op'] ? $_GET['op'] : 'list';
$pageindex = $_GET['page'] ? $_GET['page'] : 1;
$pageurl = "index.php?action=".$action."&op=".$operation;
$pagesize = 10;
$link[] = array('text' => '返回选手列表', 'href' => 'index.php?action=player&op=list&page='.$pageindex);
$_G['imgroot'] = str_replace('10.211.8.42','static.scms.sztv.com.cn',$_G['imgroot']);
$smarty->assign('imgroot',$_G['imgroot']."/");
$smarty->assign('user_GROUP', $user_GROUP);
$smarty->assign('page', $pageindex);
if ($operation == 'list')
{
	admin_priv('player');

    /* 查询数据库中管理员列表 */
	$result =  C::t("player")->fetch_player_list($pageindex,$pagesize,1);
	$total = $result['total'];
	$multi = multi($total, $pagesize, $pageindex, $pageurl);
	$smarty->assign('player', $result['res']);	
	$smarty->assign('multi', $multi);
}elseif($operation == 'edit'){	
	if($_POST['Submit']){
		$name = $_POST['name'];
		//$user_group = $_POST['user_group'];
		$user_group = 1;
		$id = intval($_POST['id']);
		$desc = $_POST['desc'];
		$poll_num = intval($_POST['poll_num']);
		$xuhao = $_POST['xuhao'];
		$detail = $_POST['detail'];
		$img = $_FILES['img'];
		if($img['size'] > 0){
			$data['img'] = $image->upload_image($img,'sign',date("YmdHis").'.jpg');
			$data['img'] = str_replace('/temp/','temp/',$data['img']);
		}
		$data['name'] = $name;
		$data['poll_num'] = $poll_num;
		$data['desc'] = $desc;
		$data['xuhao'] = $xuhao;
		$data['detail'] = $detail;
		$data['user_group'] = $user_group;
		$res = C::t('player')->update($data,array('id'=>$id));
		//写入日志
		admin_log("编辑选手,ID:".$id);
		//if($res){
			sys_msg('编辑成功', 0, $link);
		//}else{
			//sys_msg('编辑失败', 0, $link);
		//}
	}else{
		$id = intval($_GET['id']);
		$info = C::t("player")->fetch_by_id($id);
		$smarty->assign('info', $info);
	}
}elseif($operation == 'add'){
	if($_POST['Submit']){
		$name = $_POST['name'];
		//$user_group = $_POST['user_group'];
		$user_group = 1;
		$desc = $_POST['desc'];
		$xuhao = $_POST['xuhao'];
		$poll_num = intval($_POST['poll_num']);
		$detail = $_POST['detail'];
		$img = $_FILES['img'];
		if($img['size'] > 0){
			$data['img'] = $image->upload_image($img,'sign',date("YmdHis").'.jpg');
			$data['img'] = str_replace('/temp/','temp/',$data['img']);
		}
		$data['name'] = $name;
		$data['xuhao'] = $xuhao;
		$data['poll_num'] = $poll_num;
		$data['desc'] = $desc;
		$data['detail'] = $detail;
		$data['user_group'] = $user_group;
		$res = C::t('player')->insert($data);
		//if($res){
			sys_msg('添加成功', 0, $link);
		//}else{
			//sys_msg('添加失败', 0, $link);
		//}
	}
}