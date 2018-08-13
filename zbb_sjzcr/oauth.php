<?php
require_once './source/class/class_core.php';
$kldapp = C::app();
$kldapp->reject_robot();
$kldapp->init();
require_once "./source/function/function_wx.php";
header("Content-type:text/html;charset=utf-8");
$code = $_GET['code'];
$state = $_GET['state'];
//换成自己的接口信息
$appid = $_G['config']['WX']['APPID'];
$appkey = $_G['config']['WX']['APPSECRET'];
if (empty($code)) exit('授权失败');
//获取授权
$access_token = getNewAccessToken($code);
if (isset($access_token->errcode)) {
    $msg =  '<h1>错误1：</h1>'.$access_token->errcode;
    $msg .='<br/><h2>错误信息1：</h2>'.$access_token->errmsg;
    show_msg($msg);
}
//获取用户信息
$weixinUser = getWeiXinUser($access_token->openid,$access_token->access_token);
if (isset($weixinUser->errcode)) {
    $msg = '<h1>错误2：</h1>'.$weixinUser->errcode;
    $msg .='<br/><h2>错误信息2：</h2>'.$weixinUser->errmsg;
    show_msg($msg);
}
mySetCookie('openid',$weixinUser->openid);
mySetCookie('subscribe',$weixinUser->subscribe);
mySetCookie('nickname',$weixinUser->nickname);
$data = array(
	'openid'=>$weixinUser->openid,
	'headimgurl'=>$weixinUser->headimgurl,
	'nickname'=>$weixinUser->nickname
);
C::t('user')->insert($data);
$return_url = urldecode($_GET['return_url']);
if($return_url){
    header('location:'.$return_url);
}else{
    header('location:index.php');
}
?>