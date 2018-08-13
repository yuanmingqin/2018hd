<?php
/**
 *    秘密信息
 *    
 *   异常处理语言包
 */
if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

$lang = array
(
	'System Message' => '站点信息',

	'config_notfound' => '配置文件 "config.php" 未找到或者无法访问',
	'template_notfound' => '模版文件未找到或者无法访问',
	'directory_notfound' => '目录未找到或者无法访问',
	'access_notfound' => '请求来源非法，已经被系统拒绝',
	'request_invalid' => '无效请求',
	'request_tainting' => '您当前的访问请求当中含有非法字符，已经被系统拒绝',
	'db_help_link' => '点击这里寻求帮助',
	'db_error_message' => '错误信息',
	'db_error_sql' => '<b>SQL</b>: $sql<br />',
	'db_error_backtrace' => '<b>Backtrace</b>: $backtrace<br />',
	'db_error_no' => '错误代码',
	'db_notfound_config' => '配置文件 "config.php" 未找到或者无法访问。',
	'db_notconnect' => '无法连接到数据库服务器',
	'db_security_error' => '查询语句安全威胁',
	'db_query_sql' => '查询语句',
	'db_query_error' => '查询语句错误',
	'db_config_db_not_found' => '数据库配置错误，请仔细检查 config.php 文件',
	'system_init_ok' => '网站系统初始化完成，请<a href="index.php">点击这里</a>进入',
	'backtrace' => '运行信息',
	'error_end_message' => '<a href="http://{host}">{host}</a> 已经将此出错信息详细记录, 由此给您带来的访问不便我们深感歉意',
);

?>