<?php
require_once './source/class/class_core.php';
$kldapp = C::app();
$kldapp->init();
$is_yidong = is_yidong();
if(!$is_yidong){
    show_msg("请在手机端打开"); 
}
//防止搜索引擎抓取
$_G['imgroot'] = str_replace('10.211.8.42','static.scms.sztv.com.cn',$_G['imgroot']);
$smarty->assign('imgroot',$_G['imgroot']);

//获取缓存
$id = intval($_GET['id']);
$info  = C::t('player')->fetch_by_id($id);
if(!$info){
    show_msg("选手不存在");
}
$is_weixin = is_weixin();
$openid = $_COOKIE['openid'];
if($is_weixin && !$openid){
	header('location:do.php?return_url='.urlencode('detail.php?id='.$id));
}
//分享
$appid = $_G['config']['WX']['APPID'];
$appkey = $_G['config']['WX']['APPSECRET'];
require_once "jssdk.php";
$jssdk = new JSSDK($appid,$appkey);
$signPackage = $jssdk->GetSignPackage();
$smarty->assign('signPackage', json_encode($signPackage));
$smarty->assign('info', $info);
$smarty->assign('id', $id);
$smarty->assign('name', $info['name']);
$smarty->assign('user_group', $info['user_group']);
//缓存ip
$ip = getRealIp();
mySetCookie('ip',$ip);
$smarty->display('detail.html');
?>