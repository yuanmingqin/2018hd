<?php
/**
 *    秘密信息 
 */
if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}
function updateWxUser($weixinUser){
	$data = array(
		'headimgurl'=>$weixinUser->headimgurl,
		'nickname'=>$weixinUser->nickname
	);
	$condition = array('openid'=>$weixinUser->openid);
	//C::t('user')->update($data, $condition);
}
function getAccessToken($code){
	global $_G;
	$appid = $_G['config']['WX']['APPID'];
	$appkey = $_G['config']['WX']['APPSECRET'];
	$token_url = $_G['config']['WX']['weioauth_sns'].'access_token?appid='.$appid.'&secret='.$appkey.'&code='.$code.'&grant_type=authorization_code';
	$token = json_decode(httpGet($token_url));
	$access_token_url = $_G['config']['WX']['weioauth_sns'].'refresh_token?appid='.$appid.'&grant_type=refresh_token&refresh_token='.$token->refresh_token;
	//转成对象
	$access_token = json_decode(httpGet($access_token_url));
	return $access_token;
}
function getWeiXinUser($openid,$token){
	global $_G;
	require_once CUTV_ROOT."./jssdk.php";
	$appid = $_G['config']['WX']['APPID'];
	$appkey = $_G['config']['WX']['APPSECRET'];
	$jssdk = new JSSDK($appid,$appkey);
	$client_token = $jssdk->getAccessToken();
	$subscribe_url = $_G['config']['WX']['weixinapi']."user/info?access_token=".$client_token."&openid=".$openid."&lang=zh_CN";
	$user_info_url = $_G['config']['WX']['weixinapi_sns']."userinfo?access_token=".$token."&openid=".$openid."&lang=zh_CN";
	$weixinUser = json_decode(httpGet($user_info_url));
	$subscribe_info = json_decode(httpGet($subscribe_url));
	$weixinUser->subscribe = $subscribe_info->subscribe;
	return $weixinUser;
}
function getSignPackage(){
	global $_G;
	require_once CUTV_ROOT."./jssdk.php";
	$appid = $_G['config']['WX']['APPID'];
	$appkey = $_G['config']['WX']['APPSECRET'];
	$jssdk = new JSSDK($appid,$appkey);
	return $jssdk->GetSignPackage();
} 
?>