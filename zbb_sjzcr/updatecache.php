<?php
/**
 *    秘密信息
 *    
 *    更新缓存
 */
require_once './source/class/class_core.php';

$kldapp = C::app();
//防止搜索引擎抓取
$kldapp->reject_robot();

$f = CUTV_ROOT . '/temp/cache_time.php';
if(!file_exists($f)){
	file_put_contents($f, "<?php define('CACHE_TIME', '1356969600'); ?>"); //2013-01-01 00:00:00
}
require_once($f);//加载cache_time.php

if(!defined('CACHE_TIME') || CACHE_TIME < (time()-60)) //刷新缓存周期为1分钟
{
	clear_all_files();

	file_put_contents($f, str_replace("'CACHE_TIME', '".CACHE_TIME."'", "'CACHE_TIME', '".time()."'",file_get_contents($f)));
	
	echo date('Y-m-d H:i:s', time());
}
else{
	echo date('Y-m-d H:i:s', CACHE_TIME);
}
?>
