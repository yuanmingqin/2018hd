<?php
/**
 *    秘密信息
 *    
 *    动态参数获取，以及数据库引擎初始化
 */
if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

class app_application extends app_base{

	var $config = array();

	var $var = array();

	var $init_db = true;
    var $init_cron = true;
	var $initated = false;

	var $superglobal = array(
		'GLOBALS' => 1,
		'_GET' => 1,
		'_POST' => 1,
		'_REQUEST' => 1,
		'_COOKIE' => 1,
		'_SERVER' => 1,
		'_ENV' => 1,
		'_FILES' => 1,
	);

	static function &instance() {
		static $object;
		if(empty($object)) {
			$object = new self();
		}
		return $object;
	}

	public function __construct() {
		$this->_init_env();
		$this->_init_constant();
		$this->_init_config();
		$this->_init_input();
		setglobal('gzipcompress', 0);
	}

	public function init() {
		if(!$this->initated) {
			$this->_init_db();
			$this->_init_cron();
		}
		$this->initated = true;
	}

	private function _init_env() {

		error_reporting(E_ERROR);
		if(PHP_VERSION < '5.3.0') {
			set_magic_quotes_runtime(0);
		}

		define('MAGIC_QUOTES_GPC', function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
		define('ICONV_ENABLE', function_exists('iconv'));
		define('MB_ENABLE', function_exists('mb_convert_encoding'));
		define('EXT_OBGZIP', function_exists('ob_gzhandler'));

		define('TIMESTAMP', time());
		$this->timezone_set(TIMEZONE);

		if(!defined('CUTV_CORE_FUNCTION') && !@include(CUTV_ROOT.'./source/function/function_core.php')) {
			exit('function_core.php is missing');
		}

		if(function_exists('ini_get')) {
			$memorylimit = @ini_get('memory_limit');
			if($memorylimit && return_bytes($memorylimit) < 33554432 && function_exists('ini_set')) {
				ini_set('memory_limit', '128m');
			}
		}
		
		define('IS_ROBOT', checkrobot());

		foreach ($GLOBALS as $key => $value) {
			if (!isset($this->superglobal[$key])) {
				$GLOBALS[$key] = null; unset($GLOBALS[$key]);
			}
		}

		global $_G;
		$_G = array(
			'timestamp' => TIMESTAMP,
			'starttime' => microtime(true),
			'clientip' => $this->_get_client_ip(),
			'gzipcompress' => '',

			'PHP_SELF' => '',
			'siteurl' => '',
			'imgroot' => '',
			'siteroot' => '',
			'siteport' => '',
			'server_addr' => '',
			'config' => array(),
			'custom' => array(),
			'custom_reply' => array(),
			'lang' => array(),
		);

		$_G['PHP_SELF'] = dhtmlspecialchars($this->_get_script_url());
		$_G['basescript'] = CURSCRIPT;
		$_G['basefilename'] = basename($_G['PHP_SELF']);
		$sitepath = substr($_G['PHP_SELF'], 0, strrpos($_G['PHP_SELF'], '/'));
		$_G['siteurl'] = dhtmlspecialchars('http://'.$_SERVER['HTTP_HOST'].$sitepath.'/');
		
		$_G['server_addr'] = date('Y-m-d H:i:s', time());
		$server_addr = $_SERVER['SERVER_ADDR'];
		if($server_addr){
			$saddr = explode(".",$server_addr);
			if(count($saddr) == 4){
				$_G['server_addr'] .= ".".$saddr[3];
			}
		}
		
		$url = parse_url($_G['siteurl']);
		$_G['siteroot'] = isset($url['path']) ? $url['path'] : '';
		$_G['siteport'] = empty($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT'];

		if(defined('SUB_DIR')) {
			$_G['siteurl'] = str_replace(SUB_DIR, '/', $_G['siteurl']);
			$_G['siteroot'] = str_replace(SUB_DIR, '/', $_G['siteroot']);
		}

		$tmpurl = $_G['siteurl'];
		if(substr($tmpurl, (strlen($tmpurl)-1)) == "/"){
			$tmpurl = substr($tmpurl, 0, -1);
		}
		$_G['siteurl'] = $tmpurl;
		
		$this->var = & $_G;

	}
	//常量表
	private function _init_constant(){
		//公众号
		define('WEIXIN_OPENID_KLDLK', 'gh_9e03b3f8a4d8');  //路况微信
		define('WEIXIN_OPENID_CARELAND', 'gh_5f92d2782ea8');  //官方微信
	}
	private function _init_cron() {
	}
	private function _get_script_url() {
		if(!isset($this->var['PHP_SELF'])){
			$scriptName = basename($_SERVER['SCRIPT_FILENAME']);
			if(basename($_SERVER['SCRIPT_NAME']) === $scriptName) {
				$this->var['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
			} else if(basename($_SERVER['PHP_SELF']) === $scriptName) {
				$this->var['PHP_SELF'] = $_SERVER['PHP_SELF'];
			} else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName) {
				$this->var['PHP_SELF'] = $_SERVER['ORIG_SCRIPT_NAME'];
			} else if(($pos = strpos($_SERVER['PHP_SELF'],'/'.$scriptName)) !== false) {
				$this->var['PHP_SELF'] = substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
			} else if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT']) === 0) {
				$this->var['PHP_SELF'] = str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
				$this->var['PHP_SELF'][0] != '/' && $this->var['PHP_SELF'] = '/'.$this->var['PHP_SELF'];
			} else {
				system_error('request_tainting');
			}
		}
		return $this->var['PHP_SELF'];
	}

	private function _init_input() {
		if (isset($_GET['GLOBALS']) ||isset($_POST['GLOBALS']) ||  isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS'])) {
			system_error('request_tainting');
		}

		if(MAGIC_QUOTES_GPC) {
			$_GET = dstripslashes($_GET);
			$_POST = dstripslashes($_POST);
			$_COOKIE = dstripslashes($_COOKIE);
		}

		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
			$_GET = array_merge($_GET, $_POST);
		}
	}

	private function _init_config() {
		$_config = array();
		@include CUTV_ROOT.'./config/config.php';
		if(empty($_config)) {
			system_error('config_notfound');
		}

		if(empty($_config['debug']) || !file_exists(libfile('function/debug'))) {
			define('CUTV_DEBUG', false);
			error_reporting(0);
		} elseif($_config['debug'] === 1 || $_config['debug'] === 2 || !empty($_REQUEST['debug']) && $_REQUEST['debug'] === $_config['debug']) {
			define('CUTV_DEBUG', true);
			error_reporting(E_ERROR);
			if($_config['debug'] === 2) {
				error_reporting(E_ALL);
			}
		} else {
			define('CUTV_DEBUG', false);
			error_reporting(0);
		}
		
		$_config['sysconfig']['OpenID'] = array(
			WEIXIN_OPENID_CARELAND => array(
				'code' => 'careland',
				'text' => '官方微信'
			),
			WEIXIN_OPENID_KLDLK => array(
				'code' => 'kldlk',
				'text' => '路况微信'
			)
		);
        
		$this->config = & $_config;
		$this->var['config'] = & $_config;
		
		$this->var['imgroot'] = $_config['FTP']['FTP_ON']?$_config['FTP']['ATTACH_URL']:str_replace("/admin","",$this->var['siteurl']);
	}

	public function reject_robot() {
		if(IS_ROBOT) {
			exit(header("HTTP/1.1 403 Forbidden"));
		}
	}

	private function _get_client_ip() {
		$ip = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
			foreach ($matches[0] AS $xip) {
				if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
					$ip = $xip;
					break;
				}
			}
		}
		return $ip;
	}

	private function _init_db() {
		if($this->init_db) {
			$driver = 'db_driver_mysql';
			DB::init($driver, $this->config['db']);
		    $this->get_custom_config();
		    $this->get_custom_reply();
		}
	}
	
	private function get_custom_config(){
	}
	private function get_custom_reply(){
	}

	public function timezone_set($timeoffset = 0) {
		if(function_exists('date_default_timezone_set')) {
			@date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
		}
	}
}

?>