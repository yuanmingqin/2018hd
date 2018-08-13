<?php
/**
 *    秘密信息
 *    
 *   核心类(入口)
 */
error_reporting(E_ALL);

define('IN_CUTVSYS', true);
define('CUTV_ROOT', substr(dirname(__FILE__), 0, -12));
define('CUTV_CORE_DEBUG', false);
define("TIMEZONE", 8); //时区为东八区

set_exception_handler(array('core', 'handleException'));

if(CUTV_CORE_DEBUG) {
	set_error_handler(array('core', 'handleError'));
	register_shutdown_function(array('core', 'handleShutdown'));
}

if(function_exists('spl_autoload_register')) {
	spl_autoload_register(array('core', 'autoload'));
} else {
	function __autoload($class) {
		return core::autoload($class);
	}
}

C::creatapp();

class core
{
	private static $_tables;
	private static $_imports;
	private static $_app;

	public static function app() {
		return self::$_app;
	}

	public static function creatapp() {
		if(!is_object(self::$_app)) {
			self::$_app = app_application::instance();
		}
		return self::$_app;
	}

	public static function t($name) {
		$classname = 'table_'.$name;
		if(!isset(self::$_tables[$classname])) {
			self::$_tables[$classname] = new $classname;
		}
		return self::$_tables[$classname];
	}

	public static function import($name, $folder = '', $force = true) {
		$key = $folder.$name;
		if(!isset(self::$_imports[$key])) {
			$path = CUTV_ROOT.'/source/'.$folder;
			if(strpos($name, '/') !== false) {
				$pre = basename(dirname($name));
				$filename = dirname($name).'/'.$pre.'_'.basename($name).'.php';
			} else {
				$filename = $name.'.php';
			}

			if(is_file($path.'/'.$filename)) {
				self::$_imports[$key] = true;
				return include $path.'/'.$filename;
			} elseif(!$force) {
				return false;
			} else {
				throw new Exception('Oops! System file lost: '.$filename);
			}
		}
		return true;
	}

	public static function handleException($exception) {
		app_error::exception_error($exception);
	}

	public static function handleError($errno, $errstr, $errfile, $errline) {
		if($errno & CUTV_CORE_DEBUG) {
			app_error::system_error($errstr, false, true, false);
		}
	}

	public static function handleShutdown() {
		if(($error = error_get_last()) && $error['type'] & CUTV_CORE_DEBUG) {
			app_error::system_error($error['message'], false, true, false);
		}
	}

	public static function autoload($class) {
		if ((class_exists($class)) || (strpos($class, 'PHPExcel') === 0)) {
			//	Either already loaded, or a PHPExcel class request
			return FALSE;
		}
		
		$class = strtolower($class);
		if(strpos($class, '_') !== false) {
			list($folder) = explode('_', $class);
			$file = 'class/'.$folder.'/'.substr($class, strlen($folder) + 1);
		} else {
			$file = 'class/'.$class;
		}

		try {
			self::import($file);
			return true;
		} catch (Exception $exc) {
			$trace = $exc->getTrace();
			foreach ($trace as $log) {
				if(empty($log['class']) && $log['function'] == 'class_exists') {
					return false;
				}
			}
			app_error::exception_error($exc);
		}
	}
}

class C extends core {}
class DB extends app_database {}

$smarty = new app_template;
//$_G['siteurl'] = str_replace('10.211.8.42','dyxc.scms.sztv.com.cn',$_G['siteurl']);
//$_G['imgroot'] = str_replace('10.211.8.42','dyxc.scms.sztv.com.cn',$_G['imgroot']);
?>