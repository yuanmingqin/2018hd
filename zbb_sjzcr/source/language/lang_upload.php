<?php
/**
 *    秘密信息
 *    
 *   app_image类的语言项
 */
if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}


$lang =array();
$lang['directory_readonly'] = '目录 % 不存在或不可写';
$lang['invalid_upload_image_type'] = '不是允许的图片格式';
$lang['upload_failure'] = '文件 %s 上传失败。';
$lang['missing_gd'] = '没有安装GD库';
$lang['missing_orgin_image'] = '找不到原始图片 %s ';
$lang['nonsupport_type'] = '不支持该图像格式 %s ';
$lang['creating_failure'] = '创建图片失败';
$lang['writting_failure'] = '图片写入失败';
$lang['empty_watermark'] = '水印文件参数不能为空';
$lang['missing_watermark'] = '找不到水印文件%s';
$lang['create_watermark_res'] = '创建水印图片资源失败。水印图片类型为%s';
$lang['create_origin_image_res'] = '创建原始图片资源失败，原始图片类型%s';
$lang['invalid_image_type'] = '无法识别水印图片 %s ';
$lang['file_unavailable'] = '文件 %s 不存在或不可读';

?>