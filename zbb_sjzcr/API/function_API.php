<?php
/**
* 验证手机号是否正确
*/
function isMobile($mobile) {
	
    if (!is_numeric($mobile)) {
        return false;
    }
    if(preg_match("/^1[0-9]{2}[0-9]{8}$/",$mobile)){
		return true;
	}else{
		return false;
	}
}
function check_can_qiangpiao($arr){
	$nowHour = date("G");
	foreach($arr as $key=>$v){
		if(($nowHour >= $v['start'] && $nowHour <= $v['end'])&& $v['num'] > 0){
			return $key;
			break;
		}
	}
	return false;
}

?>