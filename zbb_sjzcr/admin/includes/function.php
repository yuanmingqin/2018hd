<?php 
/**
 *    秘密信息
 *    
 *    后台配置通用方法
 */
if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}
//检查内置配置选项
function check_config_option($arr){
	if(!$arr){
		return -1;
	}
	else if(check_is_empty($arr)){
		return -2;
	}else{
		foreach($arr as $k=>$v){
			if(!$v['title']){
				return -3;
			}
			if(get_valid_count($v['title'])<2){
				return -4;
			}
		}
		return 0;
	}
}
//检查自定义回复选项
function check_cureply_option($title,$content,$url){
	if(!$title && !$content && !$url){
		return -2;
	}else if(check_ctitle_empty($title)){
		return -3;
	}else if(get_valid_count($title)<2){
		return -4;
	}else{
		return 0;
	}
}
/**检查内置多图文回复选项是否为空
 */
function check_is_empty($arr) {
	foreach($arr as $v){
		if(!$v['title'] && !$v['content'] && !$v['url']){
			return true;
		}
		
	}
	return false;
}
/**检查自定义多图文回复标题是否为空
 */
function check_ctitle_empty($arr) {
	foreach($arr as $v){
		if(!$v){
			return true;
		}
		
	}
	return false;
}

/**获取数组中的非空值个数
 */
function get_valid_count($arr) {
	if(!is_array($arr)){
		return 0 ;
	}
	$i=0;
	foreach($arr as $v){
		if($v!=''){
			$i++;
		}
		
	}
	return $i;
}
?>