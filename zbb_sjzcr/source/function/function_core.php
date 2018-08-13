<?php
/**
 *    秘密信息
 *    
 *   公共方法类
 */
if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

define('CUTV_CORE_FUNCTION', true);

function system_error($message, $show = true, $save = true, $halt = true) {
	app_error::system_error($message, $show, $save, $halt);
}

function setglobal($key , $value, $group = null) {
	global $_G;
	$key = explode('/', $group === null ? $key : $group.'/'.$key);
	$p = &$_G;
	foreach ($key as $k) {
		if(!isset($p[$k]) || !is_array($p[$k])) {
			$p[$k] = array();
		}
		$p = &$p[$k];
	}
	$p = $value;
	return true;
}

function getglobal($key, $group = null) {
	global $_G;
	$key = explode('/', $group === null ? $key : $group.'/'.$key);
	$v = &$_G;
	foreach ($key as $k) {
		if (!isset($v[$k])) {
			return null;
		}
		$v = &$v[$k];
	}
	return $v;
}

function getgpc($k, $type='GP') {
	$type = strtoupper($type);
	switch($type) {
		case 'G': $var = &$_GET; break;
		case 'P': $var = &$_POST; break;
		case 'C': $var = &$_COOKIE; break;
		default:
			if(isset($_GET[$k])) {
				$var = &$_GET;
			} else {
				$var = &$_POST;
			}
			break;
	}

	return isset($var[$k]) ? $var[$k] : NULL;

}

function daddslashes($string, $force = 1) {
	if(is_array($string)) {
		$keys = array_keys($string);
		foreach($keys as $key) {
			$val = $string[$key];
			unset($string[$key]);
			$string[addslashes($key)] = daddslashes($val, $force);
		}
	} else {
		$string = addslashes($string);
	}
	return $string;
}

function dhtmlspecialchars($string, $flags = null) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val, $flags);
		}
	} else {
		if($flags === null) {
			$string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
			if(strpos($string, '&amp;#') !== false) {
				$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
			}
		} else {
			if(PHP_VERSION < '5.4.0') {
				$string = htmlspecialchars($string, $flags);
			} else {
				$charset = 'UTF-8';
				$string = htmlspecialchars($string, $flags, $charset);
			}
		}
	}
	return $string;
}

function fileext($filename) {
	return addslashes(strtolower(substr(strrchr($filename, '.'), 1, 10)));
}

function checkrobot($useragent = '') {
	static $kw_spiders = array('bot', 'crawl', 'spider' ,'slurp', 'sohu-search', 'lycos', 'robozilla');
	static $kw_browsers = array('msie', 'netscape', 'opera', 'konqueror', 'mozilla');

	$useragent = strtolower(empty($useragent) ? $_SERVER['HTTP_USER_AGENT'] : $useragent);
	if(strpos($useragent, 'http://') === false && dstrpos($useragent, $kw_browsers)) return false;
	if(dstrpos($useragent, $kw_spiders)) return true;
	return false;
}

function dstrpos($string, &$arr, $returnvalue = false) {
	if(empty($string)) return false;
	foreach((array)$arr as $v) {
		if(strpos($string, $v) !== false) {
			$return = $returnvalue ? $v : true;
			return $return;
		}
	}
	return false;
}

function random($length, $numeric = 0) {
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	if($numeric) {
		$hash = '';
	} else {
		$hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
		$length--;
	}
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}

function strexists($string, $find) {
	return !(strpos($string, $find) === FALSE);
}

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}

function lang($file, $langvar = null, $vars = array(), $default = null) {
	global $_G;
	list($path, $file) = explode('/', $file);
	if(!$file) {
		$file = $path;
		$path = '';
	}

	$key = $path == '' ? $file : $path.'_'.$file;
	if(!isset($_G['lang'][$key])) {
		include CUTV_ROOT.'./source/language/'.($path == '' ? '' : $path.'/').'lang_'.$file.'.php';
		$_G['lang'][$key] = $lang;
	}
	$returnvalue = &$_G['lang'];
	
	$return = $langvar !== null ? (isset($returnvalue[$key][$langvar]) ? $returnvalue[$key][$langvar] : null) : $returnvalue[$key];
	$return = $return === null ? ($default !== null ? $default : $langvar) : $return;
	$searchs = $replaces = array();
	if($vars && is_array($vars)) {
		foreach($vars as $k => $v) {
			$searchs[] = '{'.$k.'}';
			$replaces[] = $v;
		}
	}
	if(is_string($return) && strpos($return, '{_G/') !== false) {
		preg_match_all('/\{_G\/(.+?)\}/', $return, $gvar);
		foreach($gvar[0] as $k => $v) {
			$searchs[] = $v;
			$replaces[] = getglobal($gvar[1][$k]);
		}
	}
	$return = str_replace($searchs, $replaces, $return);
	return $return;
}

function libfile($libname, $folder = '') {
	$libpath = '/source/'.$folder;
	if(strstr($libname, '/')) {
		list($pre, $name) = explode('/', $libname);
		$path = "{$libpath}/{$pre}/{$pre}_{$name}";
	} else {
		$path = "{$libpath}/{$libname}";
	}
	return preg_match('/^[\w\d\/_]+$/i', $path) ? realpath(CUTV_ROOT.$path.'.php') : false;
}

function cutstr($string, $length, $dot = ' ...') {
	if(strlen($string) <= $length) {
		return $string;
	}

	$pre = chr(1);
	$end = chr(1);
	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

	$strcut = '';
	
	$n = $tn = $noc = 0;
	while($n < strlen($string)) {

		$t = ord($string[$n]);
		if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
			$tn = 1; $n++; $noc++;
		} elseif(194 <= $t && $t <= 223) {
			$tn = 2; $n += 2; $noc += 2;
		} elseif(224 <= $t && $t <= 239) {
			$tn = 3; $n += 3; $noc += 2;
		} elseif(240 <= $t && $t <= 247) {
			$tn = 4; $n += 4; $noc += 2;
		} elseif(248 <= $t && $t <= 251) {
			$tn = 5; $n += 5; $noc += 2;
		} elseif($t == 252 || $t == 253) {
			$tn = 6; $n += 6; $noc += 2;
		} else {
			$n++;
		}

		if($noc >= $length) {
			break;
		}

	}
	if($noc > $length) {
		$n -= $tn;
	}

	$strcut = substr($string, 0, $n);

	$strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

	$pos = strrpos($strcut, chr(1));
	if($pos !== false) {
		$strcut = substr($strcut,0,$pos);
	}
	return $strcut.$dot;
}

function dstripslashes($string) {
	if(empty($string)) return $string;
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dstripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}

function dintval($int, $allowarray = false) {
	$ret = intval($int);
	if($int == $ret || !$allowarray && is_array($int)) return $ret;
	if($allowarray && is_array($int)) {
		foreach($int as &$v) {
			$v = dintval($v, true);
		}
		return $int;
	} elseif($int <= 0xffffffff) {
		$l = strlen($int);
		$m = substr($int, 0, 1) == '-' ? 1 : 0;
		if(($l - $m) === strspn($int,'0987654321', $m)) {
			return $int;
		}
	}
	return $ret;
}

function debug($var = null, $vardump = false) {
	echo '<pre>';
	$vardump = empty($var) ? true : $vardump;
	if($vardump) {
		var_dump($var);
	} else {
		print_r($var);
	}
	exit();
}

function browserversion($type) {
	static $return = array();
	static $types = array('ie' => 'msie', 'firefox' => '', 'chrome' => '', 'opera' => '', 'safari' => '', 'mozilla' => '', 'webkit' => '', 'maxthon' => '', 'qq' => 'qqbrowser');
	if(!$return) {
		$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$other = 1;
		foreach($types as $i => $v) {
			$v = $v ? $v : $i;
			if(strpos($useragent, $v) !== false) {
				preg_match('/'.$v.'(\/|\s)([\d\.]+)/i', $useragent, $matches);
				$ver = $matches[2];
				$other = $ver !== 0 && $v != 'mozilla' ? 0 : $other;
			} else {
				$ver = 0;
			}
			$return[$i] = $ver;
		}
		$return['other'] = $other;
	}
	return $return[$type];
}

/**
 * 过滤用户输入的基本数据，防止script攻击
 *
 * @access      public
 * @return      string
 */
function compile_str($str)
{
    $arr = array('<' => '＜', '>' => '＞');

    return strtr($str, $arr);
}

/**
 *  清除指定后缀的模板缓存或编译文件
	*
 * @access  public
 * @param  bool       $is_cache  是否清除缓存还是清出编译文件
 * @param  string     $ext       需要删除的文件名，不包含后缀
	*
 * @return int        返回清除的文件个数
 */
function clear_tpl_files($is_cache = true, $ext = '')
{
    $dirs = array();
    $tmp_dir = 'temp';
    
    if ($is_cache)
    {
        $cache_dir = CUTV_ROOT . $tmp_dir . '/caches/';
        $dirs[] = $cache_dir;
        $dirs[] = $cache_dir . 'admin/';
		
        for($i = 0; $i < 16; $i++)
        {
            $hash_dir = $cache_dir . dechex($i);
            $dirs[] = $hash_dir . '/';
        }
    }
    else
    {
    	$compiled_dir = CUTV_ROOT . $tmp_dir . '/compiled/';
        $dirs[] = $compiled_dir;
        $dirs[] = $compiled_dir . 'admin/';
    }
	
    $str_len = strlen($ext);
    $count   = 0;
	
    foreach ($dirs AS $dir)
    {
        $folder = @opendir($dir);
		
        if ($folder === false)
        {
            continue;
        }
		
        while (false !== ($file = readdir($folder)))
        {
            if ($file == '.' || $file == '..' || $file == 'index.htm' || $file == 'index.html')
            {
                continue;
            }
            if (is_file($dir . $file))
            {
                /* 如果有文件名则判断是否匹配 */
                $pos = ($is_cache) ? strrpos($file, '_') : strrpos($file, '.');
				
                if ($str_len > 0 && $pos !== false)
                {
                    $ext_str = substr($file, 0, $pos);
					
                    if ($ext_str == $ext)
                    {
                        if (@unlink($dir . $file))
                        {
                            $count++;
                        }
                    }
                }
                else
                {
                    if (@unlink($dir . $file))
                    {
                        $count++;
                    }
                }
            }
        }
        closedir($folder);
    }
	
    return $count;
}

/**
 * 清除模版编译文件
	*
 * @access  public
 * @param   mix     $ext    模版文件名， 不包含后缀
 * @return  void
 */
function clear_compiled_files($ext = '')
{
    return clear_tpl_files(false, $ext);
}

/**
 * 清除缓存文件
	*
 * @access  public
 * @param   mix     $ext    模版文件名， 不包含后缀
 * @return  void
 */
function clear_cache_files($ext = '')
{
    return clear_tpl_files(true, $ext);
}

/**
 * 清除模版编译和缓存文件
	*
 * @access  public
 * @param   mix     $ext    模版文件名后缀
 * @return  void
 */
function clear_all_files($ext = '')
{
    return clear_tpl_files(false, $ext) + clear_tpl_files(true,  $ext);
}

/**
 * 自定义 header 函数，用于过滤可能出现的安全隐患
 *
 * @param   string  string  内容
 *
 * @return  void
 **/
function cutv_header($string, $replace = true, $http_response_code = 0)
{
    $string = str_replace(array("\r", "\n"), array('', ''), $string);

    if (preg_match('/^\s*location:/is', $string))
    {
        @header($string . "\n", $replace);

        exit();
    }

    if (empty($http_response_code) || PHP_VERSION < '4.3')
    {
        @header($string, $replace);
    }
    else
    {
        @header($string, $replace, $http_response_code);
    }
}

/**
 * 写日志消息
 */
function write_logresult($code, $word) {
	$content = "执行日期：".strftime("%Y%m%d%H%M%S",time())." -> ".$word;
	
	$data = "version=1&type=3&udb=$code&code=$code&name=$code&content=".$content;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://log.careland.com.cn/log_up.php');
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$response = curl_exec($ch);
	if(curl_errno($ch)) {
		$return = curl_error($ch);
	} else {
		$return = curl_multi_getcontent($ch);
	}
	curl_close($ch);
}

/**
 * PHP Crul库 模拟Post提交
 * 如果使用Crul 你需要改一改你的php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
 * 返回 $data
 */
function php_post($gateway_url, $req_data, $optional_headers = null) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $gateway_url);				//配置网关地址
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);							//设置post提交
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req_data);		    //post传输数据
	if ($optional_headers !== null){
		curl_setopt($ch, CURLOPT_HTTPHEADER, $optional_headers);
	}
	else{
		curl_setopt($ch, CURLOPT_HEADER, 0);						//过滤HTTP头
	}
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
//Post数据,可直接post数组
function post_request($url, $data, $optional_headers = null)
{
	$params = array('http' => array(
		'method' => 'POST',
		'content' => (is_array($data)? http_build_query($data) : $data),
	));
	
	if ($optional_headers !== null)
	{
		$params['http']['header'] = $optional_headers;
	}
	$ctx = stream_context_create($params);
	$fp = @fopen($url, 'rb', false, $ctx);
	if (!$fp)
	{
		return false;
	}
	$response = @stream_get_contents($fp);
	
	return $response;
}

/**
 * 获取网页数据
 */
function getUrlData($url)
{
	$ch = curl_init();
	$timeout = 10;
	//$proxy = "http://169.254.10.2:808"; // 代理服务器
	//curl_setopt ($ch, CURLOPT_PROXY, $proxy);
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$content = curl_exec($ch);
	curl_close($ch);
	if($content) {
		return $content;
	} else {
		return false;
	}
}

/**
 * 发送手机短信
	*
 * @param string $mobile  手机号
 * @param string $content 发送内容
 * @return boolean
 */
function send_sms($mobile, $content) {
	$sendurl = "http://211.147.238.86:81/SDK/Sms_Send.asp?";
	$params = array(
		'CorpID' =>'113711',
		'LoginName' =>'map',
		'passwd' =>'499232',
		'LongSms' =>'1',
		'send_no' =>$mobile,
		'msg' =>$content,
	);
	$query = http_build_query($params);
	$sendurl .= str_replace('&amp;', '&', $query);
	$iRet = file_get_contents($sendurl);
	if($iRet > 0) {
		return true;
	} else {
		return false;
	}
}

/**
 * 邮件发送
	*
 * @param: $config_SMTP[array]  邮件配置参数
 * @param: $name[string]        接收人姓名
 * @param: $email[string]       接收人邮件地址
 * @param: $subject[string]     邮件标题
 * @param: $content[string]     邮件内容
 * @param: $type[int]           0 普通邮件， 1 HTML邮件
 * @param: $notification[bool]  true 要求回执， false 不用回执
 * @param: $issmtp[bool]        true 使用smtp发送，false 使用mail发送
	*
 * @return boolean
 */
function send_mail($config_SMTP, $name, $email, $subject, $content, $type = 0, $notification=false, $issmtp=true)
{
	$charset   = "utf-8";
	/**
	 * 使用mail函数发送邮件
	 */
	if (!$issmtp && function_exists('mail'))
	{
		/* 邮件的头部信息 */
		$content_type = ($type == 0) ? 'Content-Type: text/plain; charset=' . $charset : 'Content-Type: text/html; charset=' . $charset;
		$headers = array();
		$headers[] = 'From: "' . '=?' . $charset . '?B?' . base64_encode($config_SMTP['MAIL_SENDER']) . '?='.'" <' . $config_SMTP['SMTP_MAIL'] . '>';
		$headers[] = $content_type . '; format=flowed';
		if ($notification)
		{
			$headers[] = 'Disposition-Notification-To: ' . '=?' . $charset . '?B?' . base64_encode($config_SMTP['MAIL_SENDER']) . '?='.'" <' . $config_SMTP['SMTP_MAIL'] . '>';
		}
		
		$res = @mail($email, '=?' . $charset . '?B?' . base64_encode($subject) . '?=', $content, implode("\r\n", $headers));
		
		if (!$res)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	/**
	 * 使用smtp服务发送邮件
	 */
	else
	{
		/* 邮件的头部信息 */
		$content_type = ($type == 0) ?
		'Content-Type: text/plain; charset=' . $charset : 'Content-Type: text/html; charset=' . $charset;
		$content   =  base64_encode($content);
		
		$headers = array();
		$headers[] = 'Date: ' . gmdate('D, j M Y H:i:s') . ' +0000';
		$headers[] = 'To: "' . '=?' . $charset . '?B?' . base64_encode($name) . '?=' . '" <' . $email. '>';
		$headers[] = 'From: "' . '=?' . $charset . '?B?' . base64_encode($config_SMTP['MAIL_SENDER']) . '?='.'" <' . $config_SMTP['SMTP_MAIL'] . '>';
		$headers[] = 'Subject: ' . '=?' . $charset . '?B?' . base64_encode($subject) . '?=';
		$headers[] = $content_type . '; format=flowed';
		$headers[] = 'Content-Transfer-Encoding: base64';
		$headers[] = 'Content-Disposition: inline';
		if ($notification)
		{
			$headers[] = 'Disposition-Notification-To: ' . '=?' . $charset . '?B?' . base64_encode($config_SMTP['MAIL_SENDER']) . '?='.'" <' . $config_SMTP['SMTP_MAIL'] . '>';
		}
		
		/* 获得邮件服务器的参数设置 */
		$params['host'] = $config_SMTP['SMTP_HOST'];
		$params['port'] = $config_SMTP['SMTP_PORT'];
		$params['user'] = $config_SMTP['SMTP_USER'];
		$params['pass'] = $config_SMTP['SMTP_PASS'];
		
		if (empty($params['host']) || empty($params['port']))
		{
			// 如果没有设置主机和端口直接返回 false
			// smtp_setting_error
			
			return false;
		}
		else
		{
			// 发送邮件
			if (!function_exists('fsockopen'))
			{
				//如果fsockopen被禁用，直接返回
				//disabled_fsockopen
				
				return false;
			}
			
			static $smtp;
			
			$send_params['recipients'] = $email;
			$send_params['headers']    = $headers;
			$send_params['from']       = $config_SMTP['SMTP_MAIL'];
			$send_params['body']       = $content;
			
			if (!isset($smtp))
			{
				$smtp = new app_smtp($params);
			}
			
			if ($smtp->connect() && $smtp->send($send_params))
			{
				return true;
			}
			else
			{
				$err_msg = $smtp->error_msg();
				if (empty($err_msg))
				{
					//Unknown Error
				}
				else
				{
					if (strpos($err_msg, 'Failed to connect to server') !== false)
					{
						//smtp_connect_failure
					}
					else if (strpos($err_msg, 'AUTH command failed') !== false)
					{
						//smtp_login_failure
					}
					elseif (strpos($err_msg, 'bad sequence of commands') !== false)
					{
						//smtp_refuse
					}
					else
					{
						//$err_msg
					}
				}
				
				return false;
			}
		}
	}
}

/**
 * 加密uid
	*
 * @param string $uid  用户uid
 * @return string
 */
function encodeUid($uid)
{
	return cld_authcode_map('uid='.$uid, 'ENCODE', '4EDE00998AD3DC1718F07A43BDCBE8E1', 43200);
}

/**
 * 解密uid
	*
 * @param string $key  加密字符串
 * @return int
 */
function decodeUid($key)
{
	$getkey = array();
	parse_str(cld_authcode_map($key, 'DECODE', '4EDE00998AD3DC1718F07A43BDCBE8E1', 43200), $getkey);
	if($getkey && $getkey['uid'] > 0){
		return $getkey['uid'];
	}
	return 0;
}

/**
 * 加密和解密函数
	*
 * @param string $string  内容
 * @param string $operation 操作类型
 * @param string $key       密钥
 * @param int   $expiry     有效期 
 * @return string
 */
function cld_authcode_map($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
	$ckey_length = 4;
	$key = md5($key ? $key : '');
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
	
	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	
	$result = '';
	$box = range(0, 255);
	
	$rndkey = array();
	for($i = 0; $i <= 255; $i++)
	{
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}
	
	for($j = $i = 0; $i < 256; $i++)
	{
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	
	for($a = $j = $i = 0; $i < $string_length; $i++)
	{
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	
	if($operation == 'DECODE')
	{
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16))
		{
			return substr($result, 26);
		}
		else
		{
			return '';
		}
	}
	else
	{
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}
/**
 * json字符窜转换成数组
	*
 * @param string $str  json字符窜
 * @return array
 */
function json_to_array($str) {
	if (is_string($str))
		$str = json_decode($str);
	$arr=array();
	foreach($str as $k=>$v) {
		if(is_object($v) || is_array($v))
			$arr[$k]=json_to_array($v);
		else
			$arr[$k]=$v;
	}
	return $arr;
}
/**
 * 生成编辑器
 * @param   string  input_name  输入框名称
 * @param   string  input_value 输入框值
 */
function create_html_editor($input_name, $input_value = '')
{
    global $smarty;

    $editor = new FCKeditor($input_name);
    $editor->BasePath   = '../source/include/fckeditor/';
    $editor->ToolbarSet = 'Normal';
    $editor->Width      = '100%';
    $editor->Height     = '520';
    $editor->Value      = $input_value;
    $FCKeditor = $editor->CreateHtml();
    $smarty->assign('FCKeditor_'.$input_name, $FCKeditor);
}

// K码转坐标
function kcode_decode($k)
{
	$dtResult = array();
	
	$Lon_Offset = 0;
	$Lat_Offset = 0;
	$WestEdge 	= 70 * 3600 * 10;
	$SouthEdge 	= 5 * 3600 * 10;
	$MidLine 	= 35 * 3600 * 10;
	
	$k =strtoupper($k);
	
	$n = strlen($k);
	if ($n != 9 ){
		return $dtResult;
	}
	
	$kcode = array();
	for ($i = 0; $i < $n; $i++ ){
		$kcode[$i] = substr($k,$i,1);
	}
	
	$ZoneChar = $kcode[0];
	
	if ( ('5' != $ZoneChar ) && ('6' != $ZoneChar ) && ('7' != $ZoneChar ) && ( '8' != $ZoneChar )){
		return $dtResult;
	}
	
	$n0 =  ord('0') ;
	$n9 =  ord('9') ;
	$nA =  ord('A') ;
	$nL =  ord('L') ;
	$nO =  ord('O') ;
	$nZ =  ord('Z') ;
	$nM =  ord('M') ;
	$nP =  ord('P') ;
	
	/* 计算经度*/
	for ($i = 1; $i < 5; $i++ ){
		$kn =  ord($kcode[$i]);
		
		if ( ($kn >= $n0 ) && ( $kn <= $n9) ){
			$Lon_Offset = $Lon_Offset + ($kn-$n0) * pow(34,$i-1);
		}
		
		if ( ($kn >= $nA ) && ( $kn < $nL) ){
			$Lon_Offset = $Lon_Offset + (($kn-$nA) + 10) * pow(34,$i-1);
		}
		
		if ( ($kn > $nL ) && ( $kn < $nO) ){
			$Lon_Offset = $Lon_Offset + (($kn-$nM) + 21) * pow(34,$i-1);
		}
		
		if ( ($kn > $nO ) && ( $kn <= $nZ) ){
			$Lon_Offset = $Lon_Offset + (($kn-$nP) + 23) * pow(34,$i-1);
		}
	}
	
	/* 计算纬度*/
	for ($i = 5; $i < 9; $i++ ){
		$kn =  ord($kcode[$i]);
		
		if ( ($kn >= $n0 ) && ( $kn <= $n9) ){
			$Lat_Offset = $Lat_Offset + ($kn-$n0) * pow(34,$i-5);
		}
		
		if ( ($kn >= $nA ) && ( $kn < $nL) ){
			$Lat_Offset = $Lat_Offset + (($kn-$nA) + 10) * pow(34,$i-5);
		}
		
		if ( ($kn > $nL ) && ( $kn < $nO) ){
			$Lat_Offset = $Lat_Offset + (($kn-$nM) + 21) * pow(34,$i-5);
		}
		
		if ( ($kn > $nO ) && ( $kn <= $nZ) ){
			$Lat_Offset = $Lat_Offset + (($kn-$nP) + 23) * pow(34,$i-5);
		}
	}
	
	switch ( $ZoneChar ){
		case '5':
		$resultx = $Lon_Offset + $MidLine + $WestEdge;
		$resulty = $Lat_Offset + $MidLine + $SouthEdge;
		break;
		case '6':
		$resultx =  $Lon_Offset + $WestEdge;
		$resulty = $Lat_Offset + $MidLine + $SouthEdge;
		break;
		case '7':
		$resultx = $Lon_Offset + $WestEdge;
		$resulty = $Lat_Offset + $SouthEdge;
		break;
		case '8':
		$resultx = $Lon_Offset + $MidLine + $WestEdge;
		$resulty = $Lat_Offset + $SouthEdge;
		break;
	}
	
	$resultx = ($resultx + 3337) * 100;
	$resulty = ($resulty + 2373) * 100;
	
	$dtResult['x'] = $resultx;
	$dtResult['y'] = $resulty;
	
	return $dtResult;
}

// K码编码
function coding_kcode($in_Lon, $in_Lat){
	$WestEdge 	= 70 * 3600 * 10;
	$EastEdge 	= 140 * 3600 * 10;
	$SouthEdge 	= 5 * 3600 * 10;
	$NorthEdge 	= 75 * 3600 * 10;
	$MidLine 	= 35 * 3600 * 10;
	
	if ( ($in_Lon < $WestEdge) || ($in_Lon > $EastEdge) || ($in_Lat < $SouthEdge) || ($in_Lat > $NorthEdge ))
	{
		return "100000000";
	}
	
	$out_KCode = array();
	
	$i=0;$t=0;$p=0;$ct=0;$KZone=0;$dct=0;
	
	$Lon_Offset = $in_Lon - $WestEdge;
	$Lat_Offset = $in_Lat - $SouthEdge;
	
	if ( $Lon_Offset > $MidLine )
	{
		$Lon_Offset -= $MidLine;
		if ( $Lat_Offset > $MidLine )
		{
			$Lat_Offset -= $MidLine;
			$KZone = 1;
		}
		else
		{
			$KZone = 4;
		}
	}
	else
	{
		if ( $Lat_Offset > $MidLine )
		{
			$Lat_Offset -= $MidLine;
			$KZone = 2;
		}
		else
		{
			$KZone = 3;
		}
	}
	
	$out_KCode[0] = chr(ord('4') + $KZone);
	
	$t = $Lon_Offset; $ct = 1;$dct = 0;
	
	while ( $t > 0 && $dct < 4 )
	{
		$p = $t % 34;
		if ( $p < 10 )
		{
			$out_KCode[$ct] = chr(ord('0')+ $p);
		}
		else
		{
			if ( $p < 21 )          /* 因为l(L)和很相像，不便于识别，所以剔出*/
			{
				$out_KCode[$ct] = chr(ord('A')+ $p - 10);
			}
			else
			{
				if ( $p < 23 )      /* 因为O和很相像，不便于识别，所以剔出*/
				{
					$out_KCode[$ct] = chr(ord('M')+ $p - 21);
				}
				else
				{
					$out_KCode[$ct] = chr(ord('P')+ $p - 23);
				}
			}
		}
		$ct++;$dct++;
		$t = $t / 34;
	}
	
	for ( $i = $dct; $i < 4; $i++ )          /* 高位补*/
	{
		$out_KCode[$ct] = '0';
		$ct++;
	}
	
	$t = $Lat_Offset; $dct = 0;
	
	while ( $t > 0 && $dct < 4)
	{
		$p = $t % 34;
		if ( $p < 10 )
		{
			$out_KCode[$ct] = chr(ord('0')+ $p);
		}
		else
		{
			if ( $p < 21 )          /* 因为l(L)和很相像，不便于识别，所以剔出*/
			{
				$out_KCode[$ct] = chr(ord('A')+ $p - 10);
			}
			else
			{
				if ( $p < 23 )/* 因为O和很相像，不便于识别，所以剔出*/
				{
					$out_KCode[$ct] = chr(ord('M')+ $p - 21 );
				}
				else
				{
					$out_KCode[$ct] = chr(ord('P')+ $p - 23 );
				}
			}
		}
		
		$ct++; $dct++;
		$t = $t/34;
	}
	
	for ( $i = $dct; $i < 4; $i++ )          /* 高位补*/
	{
		$out_KCode[$ct] = '0';
		$ct++;
	}
	$out_KCode[$ct] = 0;
	
	return strtolower(substr(implode("", $out_KCode),0,9));
}

// 坐标转K码
function kcode_encode($in_Lon, $in_Lat)
{
	$in_Lon = round($in_Lon/100 - 3337);
	$in_Lat = round($in_Lat/100 - 2373);
	
	return coding_kcode($in_Lon, $in_Lat);
}

// Google坐标转K码
function Google2kcode($in_Lon, $in_Lat)
{
	$in_Lon = round($in_Lon * 36000);
	$in_Lat = round($in_Lat * 36000);
	
	return coding_kcode($in_Lon, $in_Lat);
}

/**除去数组中的空值和签名参数
 * $parameter 签名参数组
 * return 去掉空值与签名参数后的新签名参数组
 */
function para_filter($parameter) {
	$para = array();
	while (list ($key, $val) = each ($parameter)) {
		if($key == "sign" || $key == "sign_type" || $val == "")continue;
		else	$para[$key] = $parameter[$key];
	}
	return $para;
}

/**对数组排序
 * $array 排序前的数组
 * return 排序后的数组
 */
function arg_sort($array) {
	ksort($array);
	reset($array);
	return $array;
}


/**生成签名结果
 * $array要签名的数组
 * return 签名结果字符串
 */
function build_mysign($sort_array,$key) {
	$prestr = create_linkstring($sort_array);     	//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串

	$prestr = $prestr.$key;							//把拼接后的字符串再与安全校验码直接连接起来

	$mysgin = md5($prestr);			    //把最终的字符串签名，获得签名结果
	return $mysgin;
}


/**把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * $array 需要拼接的数组
 * return 拼接完成以后的字符串
 */
function create_linkstring($array) {
	$arg  = "";
	while (list ($key, $val) = each ($array)) {
		$arg.=$key."=".$val."&";
	}
	$arg = substr($arg,0,-1);		     //去掉最后一个&字符
	return $arg;
}

//读取计划任务缓存
function get_cache($key){
	$filepath = CUTV_ROOT."./temp/caches/".$key.".php";
	if(file_exists($filepath)){
		$config = include$filepath;
		return $config[$key];
	}else{
		$cron = C::t('cron')->fetch_nextcron();
		if($cron){
			if(isset($cron['nextrun'])) {
				write_cache($key, $cron['nextrun']);
			} else {
				write_cache($key, TIMESTAMP + 86400 * 365);
			}
		}
		
		return '';
	}
}

//写入计划任务缓存
function write_cache($key,$value){
	$filepath = CUTV_ROOT."./temp/caches/".$key.".php";
	
    $write_array = array($key=>$value);
    $string_start   = "<?php\n return ";
	$string_process = var_export($write_array, TRUE);
	$string_end     = "\n?>";
	$string         = $string_start.$string_process.$string_end;
 
	file_put_contents($filepath, $string);
}

function dimplode($array) {
	if(!empty($array)) {
		$array = array_map('addslashes', $array);
		return "'".implode("','", is_array($array) ? $array : array($array))."'";
	} else {
		return 0;
	}
}
//将GBK转换成UTF-8
function  get_gb_to_utf8($value){
	if(!$value){
		return '';
	}
    $value_1= $value;
    $value_2   =   @iconv( "gb2312", "utf-8//IGNORE",$value_1);
    $value_3   =   @iconv( "utf-8", "gb2312//IGNORE",$value_2);
    if(strlen($value_1)   ==   strlen($value_3))
    {
	    return   $value_2;
    }else
    {
		return   $value_1;
    }
 }
 function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
}
function show_msg($msg){
	$GLOBALS['smarty']->assign('msg', $msg);
	$GLOBALS['smarty']->display('message.html');
    exit;
}
function mySetCookie($key,$value){
	$expire = time()+3600*1;//保存10天
	setcookie($key,$value,$expire,"/");
}
function setImeiCookie($key,$value){
	$expire = mktime(0,0,0,date('m'), date('d')+1, date('Y'));//保存10天
	setcookie($key,$value,$expire,"/");
}
function myDelCookie($key,$value=''){
	$expire = time()-3600*24*10;//保存10天
	setcookie($key,$value,$expire,"/");
}
function weixin_error(){
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	if (strpos($user_agent, 'MicroMessenger') === false) {
		$GLOBALS['smarty']->display('weixin_error.html');
		exit();
	}
}
function get_rand_arr($arr,$length){
	$b = array_rand($arr,$length);
	foreach($b as $k=>$v){
		$a[$v]=$arr[$v];
	}
	$a[100] = "abc";
	return $a;
}
function is_weixin(){ 
	if ( strpos($_SERVER['HTTP_USER_AGENT'], 
	'MicroMessenger') !== false ) {	
		return true;	
	}  	
	return false;	
}
function getRealIp()
{
	$res = getUrlData('http://tool.huixiang360.com/zhanzhang/ipaddress.php');
	preg_match('/\[(.*)\]/', $res, $ip);
	return  $ip[1];	
}
function is_yidong() { 
	// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
	if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
	  return true;
	} 
	// 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
	if (isset($_SERVER['HTTP_VIA'])) { 
	  // 找不到为flase,否则为true
	  return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
	} 
	// 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
	if (isset($_SERVER['HTTP_USER_AGENT'])) {
	  $clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile','MicroMessenger'); 
	  // 从HTTP_USER_AGENT中查找手机浏览器的关键字
	  if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
		return true;
	  } 
	} 
	// 协议法，因为有可能不准确，放到最后判断
	if (isset ($_SERVER['HTTP_ACCEPT'])) { 
	  // 如果只支持wml并且不支持html那一定是移动设备
	  // 如果支持wml和html但是wml在html之前则是移动设备
	  if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
		return true;
	  } 
	} 
	return false;
  }
function get_imei(){
	//$myImei = $_COOKIE['myImei'];
	if(!$myImei){
		$myImei  = 'imei_'.random(4).'_'.time();
		var_dump($myImei);
		setImeiCookie('myImei',$myImei);
	}
	return $myImei;
}
?>