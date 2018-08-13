<?php
require_once './source/class/class_core.php';
$kldapp = C::app();
$kldapp->init();
$appid = $_G['config']['WX']['APPID'];
$_G['siteurl'] = str_replace('10.211.8.42','static.scms.sztv.com.cn',$_G['siteurl']);
$redirect_uri = urlencode($_G['siteurl'].'/oauth.php');
$return_url = urldecode($_GET['return_url']);
if($return_url){
    $redirect_uri .= "?return_url=".urlencode($_GET['return_url']);
}
if(is_weixin()){
    header('location:'.$_G["config"]["WX"]["weioauth"].'?appid='.$appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state=1&connect_redirect=1#wechat_redirect');
}else{
    header('location:index_app.php');
}
?>