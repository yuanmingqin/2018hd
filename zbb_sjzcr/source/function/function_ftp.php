<?php
/**
 *    秘密信息
 *    
 *   FTP方法类
 */
if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}
/**
 * 将上传文件转移到指定位置
 *
 * @param string $file_name
 * @param string $target_name
 * @return blog
 */
function move_upload_file($file_name, $target_name = '', $isftp = true)
{
	global $_G;
	if($_G['config']['FTP']['FTP_ON'] && $isftp) {

		$ftprs = ftpcmd('upload', $file_name, $target_name);
		@unlink($file_name);

		return $ftprs;
	}
	if (function_exists("move_uploaded_file"))
	{
		if (move_uploaded_file($file_name, $target_name))
		{
			@chmod($target_name,0755);
			return true;
		}
		else if (copy($file_name, $target_name))
		{
			@chmod($target_name,0755);
			return true;
		}
	}
	elseif (copy($file_name, $target_name))
	{
		@chmod($target_name,0755);
		return true;
	}
	return false;
}
/**
 * FTP命令操作方法
 *
 * @param       string      $cmd    操作命令
 * @param       string   $source  源文件
 * @param       string   $target  目标文件
 *
 * @return  object or bool
 */

function ftpcmd($cmd = '', $source = '', $target = '') {
	static $ftp;
	global $_G;
	if(!$_G['config']['FTP']['FTP_ON']) {
		return $cmd == 'error' ? -101 : 0;
	} elseif($ftp == null) {
		$ftp_config = array(
			'on' => $_G['config']['FTP']['FTP_ON'],
			'ssl' => $_G['config']['FTP']['FTP_SSL'],
			'host' => $_G['config']['FTP']['FTP_HOST'],
			'port' => $_G['config']['FTP']['FTP_PORT'],
			'username' => $_G['config']['FTP']['FTP_USER'],
			'password' => $_G['config']['FTP']['FTP_PASSWORD'],
			'pasv' => $_G['config']['FTP']['FTP_PASVS'],
			'attachdir' => $_G['config']['FTP']['ATTACHDIR'],
			'attachurl' => $_G['config']['FTP']['ATTACH_URL'],
			'timeout' => $_G['config']['FTP']['FTP_TIMEOUT'],
			'log' => $_G['config']['FTP']['FTP_LOG'],
		);
		$ftp = & app_ftp::instance($ftp_config);
	}
	if(!$ftp->enabled) {
		return $ftp->error();
	} elseif($ftp->enabled && !$ftp->connectid) {
		$ftp->connect();
	}
	$target = $target ? $target : $source;
	$target = str_replace(CUTV_ROOT, '', $target);
	$target = str_replace('../', '', $target);
	if(substr($target, 0, 1) == "/"){
		$target = substr($target, 1);
	}
	
	if($_G['config']['FTP']['FTP_LOG']) {
		$data = date('Y-m-d H:i:s')."\t".$cmd."\t".$source."\t".$target."\r\n";
		file_put_contents(CUTV_ROOT . 'temp/ftp.log', $data, FILE_APPEND);
	}

	switch ($cmd) {
		case 'upload' : return $ftp->upload($source, $target); break;
		case 'download' : return $ftp->ftp_get($source, $target); break;
		case 'mkdir' : return $ftp->ftp_mkdir($target); break;
		case 'list' : return $ftp->ftp_nlist($target); break;
		case 'filesize' : return $ftp->ftp_size($target); break;
		case 'delete' : return $ftp->ftp_delete($target); break;
		case 'close'  : return $ftp->ftp_close(); break;
		case 'error'  : return $ftp->error(); break;
		case 'object' : return $ftp; break;
		default       : return false;
	}

}
function make_dir($folder)
{
	$reval = false;

	if (!file_exists($folder))
	{
		/* 如果目录不存在则尝试创建该目录 */
		@umask(0);

		/* 将目录路径拆分成数组 */
		preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);

		/* 如果第一个字符为/则当作物理路径处理 */
		$base = ($atmp[0][0] == '/') ? '/' : '';

		/* 遍历包含路径信息的数组 */
		foreach ($atmp[1] AS $val)
		{
			if ('' != $val)
			{
				$base .= $val;

				if ('..' == $val || '.' == $val)
				{
					/* 如果目录为.或者..则直接补/继续下一个循环 */
					$base .= '/';

					continue;
				}
			}
			else
			{
				continue;
			}

			$base .= '/';

			if (!file_exists($base))
			{
				/* 尝试创建目录，如果创建失败则继续循环 */
				if (@mkdir(rtrim($base, '/'), 0777))
				{
					@chmod($base, 0777);
					$reval = true;
				}
			}
		}
	}
	else
	{
		/* 路径已经存在。返回该路径是不是一个目录 */
		$reval = is_dir($folder);
	}

	//clearstatcache();

	return $reval;
}
/**
 * 检查文件类型
 *
 * @access      public
 * @param       string      filename            文件名
 * @param       string      realname            真实文件名
 * @param       string      limit_ext_types     允许的文件类型
 * @return      string
 */
function check_file_type($filename, $realname = '', $limit_ext_types = '')
{
	if ($realname)
	{
		$extname = strtolower(substr($realname, strrpos($realname, '.') + 1));
	}
	else
	{
		$extname = strtolower(substr($filename, strrpos($filename, '.') + 1));
	}

	if ($limit_ext_types && stristr($limit_ext_types, '|' . $extname . '|') === false)
	{
		return '';
	}

	$str = $format = '';

	$file = @fopen($filename, 'rb');
	if ($file)
	{
		$str = @fread($file, 0x400); // 读取前 1024 个字节
		@fclose($file);
	}
	else
	{
		if (stristr($filename, ROOT_PATH) === false)
		{
			if ($extname == 'jpg' || $extname == 'jpeg' || $extname == 'gif' || $extname == 'png' || $extname == 'doc' ||
			$extname == 'xls' || $extname == 'txt'  || $extname == 'zip' || $extname == 'rar' || $extname == 'ppt' ||
			$extname == 'pdf' || $extname == 'rm'   || $extname == 'mid' || $extname == 'wav' || $extname == 'bmp' ||
			$extname == 'swf' || $extname == 'chm'  || $extname == 'sql' || $extname == 'cert'|| $extname == 'pptx' ||
			$extname == 'xlsx' || $extname == 'docx')
			{
				$format = $extname;
			}
		}
		else
		{
			return '';
		}
	}

	if ($format == '' && strlen($str) >= 2 )
	{
		if (substr($str, 0, 4) == 'MThd' && $extname != 'txt')
		{
			$format = 'mid';
		}
		elseif (substr($str, 0, 4) == 'RIFF' && $extname == 'wav')
		{
			$format = 'wav';
		}
		elseif (substr($str ,0, 3) == "\xFF\xD8\xFF")
		{
			$format = 'jpg';
		}
		elseif (substr($str ,0, 4) == 'GIF8' && $extname != 'txt')
		{
			$format = 'gif';
		}
		elseif (substr($str ,0, 8) == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A")
		{
			$format = 'png';
		}
		elseif (substr($str ,0, 2) == 'BM' && $extname != 'txt')
		{
			$format = 'bmp';
		}
		elseif ((substr($str ,0, 3) == 'CWS' || substr($str ,0, 3) == 'FWS') && $extname != 'txt')
		{
			$format = 'swf';
		}
		elseif (substr($str ,0, 4) == "\xD0\xCF\x11\xE0")
		{   // D0CF11E == DOCFILE == Microsoft Office Document
			if (substr($str,0x200,4) == "\xEC\xA5\xC1\x00" || $extname == 'doc')
			{
				$format = 'doc';
			}
			elseif (substr($str,0x200,2) == "\x09\x08" || $extname == 'xls')
			{
				$format = 'xls';
			} elseif (substr($str,0x200,4) == "\xFD\xFF\xFF\xFF" || $extname == 'ppt')
			{
				$format = 'ppt';
			}
		} elseif (substr($str ,0, 4) == "PK\x03\x04")
		{
			if (substr($str,0x200,4) == "\xEC\xA5\xC1\x00" || $extname == 'docx')
			{
				$format = 'docx';
			}
			elseif (substr($str,0x200,2) == "\x09\x08" || $extname == 'xlsx')
			{
				$format = 'xlsx';
			} elseif (substr($str,0x200,4) == "\xFD\xFF\xFF\xFF" || $extname == 'pptx')
			{
				$format = 'pptx';
			}else
			{
				$format = 'zip';
			}
		} elseif (substr($str ,0, 4) == 'Rar!' && $extname != 'txt')
		{
			$format = 'rar';
		} elseif (substr($str ,0, 4) == "\x25PDF")
		{
			$format = 'pdf';
		} elseif (substr($str ,0, 3) == "\x30\x82\x0A")
		{
			$format = 'cert';
		} elseif (substr($str ,0, 4) == 'ITSF' && $extname != 'txt')
		{
			$format = 'chm';
		} elseif (substr($str ,0, 4) == "\x2ERMF")
		{
			$format = 'rm';
		} elseif ($extname == 'sql')
		{
			$format = 'sql';
		} elseif ($extname == 'txt')
		{
			$format = 'txt';
		}
	}

	if ($limit_ext_types && stristr($limit_ext_types, '|' . $format . '|') === false)
	{
		$format = '';
	}

	return $format;
}
/**
 * 创建文件目录
 *
 * @param       string   $source  源文件
 *
 * @return  bool
 */
 
function ftpmkdir($source) {
	global $_G;
	if($_G['config']['FTP']['FTP_ON']) {
		$ftprs = ftpcmd('mkdir', $source);
		return $ftprs;
	}

	return @mkdir($source);
}

?>