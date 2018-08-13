<?php
require_once './source/class/class_core.php';
$kldapp = C::app();
$kldapp->init();
//判断是否是微信
$is_weixin = is_weixin();
if($is_weixin){
	header('location:do.php');
}
$_G['imgroot'] = str_replace('10.211.8.42','static.scms.sztv.com.cn',$_G['imgroot']);
$smarty->assign('imgroot',$_G['imgroot']);
$smarty->assign('time',time());
//缓存ip
//$ip = getRealIp();
//mySetCookie('ip',$ip);
//增加PV
$arr['clicktime'] = date('Y-m-d');
C::t("click_count")->add_count($arr);

//访问量
$clickCount  = C::t("click_count")->fetch_count();
$smarty->assign('clickCount', $clickCount);
//投票次数
$canyuCount = C::t("player")->get_canyu_count();
$smarty->assign('canyuCount', $canyuCount);
//分享
$appid = $_G['config']['WX']['APPID'];
$appkey = $_G['config']['WX']['APPSECRET'];
require_once "jssdk.php";
$jssdk = new JSSDK($appid,$appkey);
$signPackage = $jssdk->GetSignPackage();
$smarty->assign('signPackage', json_encode($signPackage));
$smarty->display('index.html');
?>