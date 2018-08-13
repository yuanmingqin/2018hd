<?php
/**
 *    秘密信息
 *    
 *    ECSHOP 图片上传类
 */
if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}
/* 图片处理相关常数 */
define('ERR_INVALID_IMAGE',         1);
define('IMAGE_DIR', '/temp/upload');
define('ERR_NO_GD',                 2);
define('ERR_IMAGE_NOT_EXISTS',      3);
define('ERR_DIRECTORY_READONLY',    4);
define('ERR_UPLOAD_FAILURE',        5);
define('ERR_INVALID_PARAM',         6);
define('ERR_INVALID_IMAGE_TYPE',    7);
class app_img
{
    var $error_no    = 0;
    var $error_msg   = '';
    var $images_dir  = IMAGE_DIR;
    //var $data_dir    = DATA_DIR;
    var $bgcolor     = '';
    var $type_maping = array(1 => 'image/gif', 2 => 'image/jpeg', 3 => 'image/png');

    function __construct($bgcolor='')
    {
        $this->app_img($bgcolor);
    }

    function app_img($bgcolor='')
    {
        if ($bgcolor)
        {
            $this->bgcolor = $bgcolor;
        }
        else
        {
            $this->bgcolor = "#FFFFFF";
        }
    }

    /**
     * 图片上传的处理函数
     *
     * @access      public
     * @param       array       upload       包含上传的图片文件信息的数组
     * @param       array       dir          文件要上传在$this->data_dir下的目录名。如果为空图片放在则在$this->images_dir下以当月命名的目录下
     * @param       array       img_name     上传图片名称，为空则随机生成
	 * @param       boolean     isftp       是否为远程附件
     * @return      mix         如果成功则返回文件名，否则返回false
     */
    function upload_image($upload, $dir = '', $img_name = '', $isftp = true)
    {
        /* 没有指定目录默认为根目录images */
		$dir = CUTV_ROOT . $this->images_dir . '/' . $dir . '/';
        if ($img_name)
		{
			$img_name = $dir . $img_name; // 将图片定位到正确地址
		}

        /* 如果目标目录不存在，则创建它 */
        if (!file_exists($dir))
        {
            if (!make_dir($dir))
            {
                /* 创建目录失败 */
                $this->error_msg = lang('upload','directory_readonly');
                $this->error_no  = $_G['IMG']['ERR_DIRECTORY_READONLY'];

                return false;
            }
        }
        if (empty($img_name))
        {
            $img_name = date('YmdHis').random(1,10);
            $img_name = $dir . $img_name . $this->get_filetype($upload['name']);
        }
        if (!$this->check_img_type($upload['type']))
        {
            $this->error_msg = lang('upload','invalid_upload_image_type');
            $this->error_no  =  $_G['IMG']['ERR_INVALID_IMAGE_TYPE'];
            return false;
        }


        /* 允许上传的文件类型 */
        $allow_file_types = '|GIF|JPG|JEPG|PNG|BMP|SWF|';
        if (!check_file_type($upload['tmp_name'], $img_name, $allow_file_types))
        {
            $this->error_msg = lang('upload','invalid_upload_image_type');
            $this->error_no  =  $_G['IMG']['ERR_INVALID_IMAGE_TYPE'];
            return false;
        }
        if ($this->move_file($upload, $img_name, $isftp))
        {
            return str_replace(CUTV_ROOT, '', $img_name);
        }
        else
        {
            $this->error_msg =lang('upload','upload_failure');
            $this->error_no  = $_G['IMG']['ERR_UPLOAD_FAILURE'];
            return false;
        }
    }

    function error_msg()
    {
        return $this->error_msg;
    }

    /*------------------------------------------------------ */
    //-- 工具函数
    /*------------------------------------------------------ */

    /**
     * 检查图片类型
     * @param   string  $img_type   图片类型
     * @return  bool
     */
    function check_img_type($img_type)
    {
        return $img_type == 'image/pjpeg' ||
               $img_type == 'image/x-png' ||
               $img_type == 'image/png'   ||
               $img_type == 'image/gif'   ||
               $img_type == 'image/jpeg';
    }

    function random_filename()
    {
        $str = '';
        for($i = 0; $i < 9; $i++)
        {
            $str .= mt_rand(0, 9);
        }

        return gmtime() . $str;
    }

    /**
     *  生成指定目录不重名的文件名
     *
     * @access  public
     * @param   string      $dir        要检查是否有同名文件的目录
     *
     * @return  string      文件名
     */
    function unique_name($dir)
    {
        $filename = '';
        while (empty($filename))
        {
            $filename = app_img::random_filename();
            if (file_exists($dir . $filename . '.jpg') || file_exists($dir . $filename . '.gif') || file_exists($dir . $filename . '.png'))
            {
                $filename = '';
            }
        }

        return $filename;
    }

    /**
     *  返回文件后缀名，如‘.php’
     *
     * @access  public
     * @param
     *
     * @return  string      文件后缀名
     */
    function get_filetype($path)
    {
        $pos = strrpos($path, '.');
        if ($pos !== false)
        {
            return substr($path, $pos);
        }
        else
        {
            return '';
        }
    }
    function move_file($upload, $target, $isftp)
    {

    	if (isset($upload['error']) && $upload['error'] > 0)
        {
            return false;
        }
		if (!move_upload_file($upload['tmp_name'], $target, $isftp))
		{
			return false;
		}
        
        return true;
    }
}

?>