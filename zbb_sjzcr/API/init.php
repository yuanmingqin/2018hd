<?php
require_once '../source/class/class_core.php';
$kldapp = C::app();
$kldapp->reject_robot();
$kldapp->init();
$return = array('errcode'=>0,'errmsg'=>'','data'=>array(),'times'=>0);
$ac = $_REQUEST['ac'];
$_G['siteurl'] = str_replace('/API','',$_G['siteurl']);
$openid = $_COOKIE['openid'];
$subscribe = $_COOKIE['subscribe'];

//$openid = 'ofyfs1IUUccteXQjJKpXHVa3pHA8';
if($ac == 'getPlayerList'){
	$user_group = $_REQUEST['user_group']?intval($_REQUEST['user_group']):1;
	$pageindex = intval($_REQUEST['page']);
	$keywords = urldecode($_REQUEST['keywords']);
	if(!$user_group){
		$return['errcode'] = -1;
		$return['errmsg'] = '参数不全';
	}else{
		if(!$keywords){
			$fileName = "../temp/caches/".$user_group."_".$pageindex.".htm";
			$needUpdate = false;
			if(!file_exists($fileName)){
				//fopen($fileName, "w");
				$needUpdate = true;
			}else{
				$lastTime = filemtime($fileName);
				if(time()-$lastTime > 600){
					$needUpdate = true;
				}				
			}
			$return['needUpdate'] = $needUpdate;
			$return['lastTime'] = $lastTime;
			if($needUpdate){
				$returnData = C::t('player')->fetch_player_list($pageindex,10,$user_group,$openid,$keywords);
				file_put_contents($fileName,json_encode($returnData));
			}else{
				$returnData = json_decode(file_get_contents($fileName));
			}
		}else{
			$returnData = C::t('player')->fetch_player_list($pageindex,10,$user_group,$openid,$keywords);
		}
		$return['data'] = $returnData;
	}
}else if($ac == 'vote'){
	$is_weixin = is_weixin();
	$id = intval($_REQUEST['id']);
	$user_group = intval($_REQUEST['user_group']);
	//$fp = fopen("lock.txt", "w+");
	//if(flock($fp,LOCK_EX | LOCK_NB))
	//{
		//非微信
		if(!$is_weixin){
			//$ip = $_COOKIE['ip'];
			//$return['data']['ip'] = $ip;
			$imei = get_imei();
			if(!$imei){
				$return['errcode'] = -1;
				$return['errmsg'] = '会话已过期，请刷新页面';
			}else{
				$times = C::t('vote')->check_is_more_from_imei($imei,$user_group);
				$return['times'] = $times;
				if($times >=3 ){
					$return['errcode'] = -2;
					if($user_group == 1){
						$return['errmsg'] = '你今天已经投过电视主持人三票了';
					}else{
						$return['errmsg'] = '你今天已经投过广播主持人三票了';
					}
					
				}else{
					C::t('player')->add_poll_num($id);
					$insert = array(
						'imei'=>$imei,
						'user_group'=>$user_group,
						'voteid'=>$id,
						'date'=>date("Y-m-d")
					);
					C::t('vote')->insert($insert);
				}
			}
		}else{
			//微信
			if(!$openid){
				$return['errcode'] = -1;
				$return['errmsg'] = '请先授权登陆';
			}else{
				$times = C::t('vote')->check_is_more($openid,$user_group);
				$return['times'] = $times;
				$return['data'] = array(
					'openid'=>$openid,
					'user_group'=>$user_group
				);
				if($times >=3 ){
					$return['errcode'] = -2;
					if($user_group == 1){
						$return['errmsg'] = '你今天已经投过电视主持人三票了';
					}else{
						$return['errmsg'] = '你今天已经投过广播主持人三票了';
					}
				}else{
					C::t('player')->add_poll_num($id);
					$insert = array(
						'openid'=>$openid,
						'voteid'=>$id,
						'user_group'=>$user_group,
						'date'=>date("Y-m-d")
					);
					C::t('vote')->insert($insert);
				}
			}
		}
		//flock($fp,LOCK_UN);
	//}else{
		//$return['errcode'] = -100;
		//$return['errmsg'] = '系统繁忙，请稍后再试';	
	//}
	//fclose($fp);
}else{
	$return['errcode'] = -404;
	$return['errmsg'] = 'error action';
}
echo json_encode($return);
exit();
?>