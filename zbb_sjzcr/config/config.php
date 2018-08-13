<?php
/**
 *    秘密信息
 *    
 *   站点配置
 */
$_config = array();

// 数据库配置
//$_config['db']['1']['dbhost'] = 'localhost';
//$_config['db']['1']['dbuser'] = 'root';
//$_config['db']['1']['dbpw'] = 'root';
$_config['db']['1']['dbhost'] = '10.211.8.42';
$_config['db']['1']['dbuser'] = 'root';
$_config['db']['1']['dbpw'] = '5079bfe061';
$_config['db']['1']['dbcharset'] = 'utf8';
$_config['db']['1']['pconnect'] = '0';
$_config['db']['1']['dbname'] = 'zbb_sjzcr';
$_config['db']['1']['tablepre'] = 'zbb_sjzcr_';

$_config['ESZ']['SERVER_URL'] = 'http://yao.cutv.com/peopleservice/usr_api_server/server.php';
$_config['ESZ']['SERVER_stxyToken'] = '6wRFYtJvVitsyCRvWdu5EVLqdv3tVe9YVPU/D5vEAvY=';

$_config['WX']['weioauth'] = 'https://open.weixin.qq.com/connect/oauth2/authorize';
$_config['WX']['weioauth_sns'] = 'https://api.weixin.qq.com/sns/oauth2/';
$_config['WX']['weixinapi'] = 'https://api.weixin.qq.com/cgi-bin/';
$_config['WX']['weixinapi_sns'] = 'https://api.weixin.qq.com/sns/';
$_config['WX']['weixinfile'] = 'http://file.api.weixin.qq.com/cgi-bin/';


//$_config['WX']['APPID'] = 'wx96d38df64fe1eb8d';
//$_config['WX']['APPSECRET'] = 'cf384de2c485d30d525cd4201dde34f0';

$_config['WX']['APPID'] = 'wx1d6e9fa7645660fe';
$_config['WX']['APPSECRET'] = '8338451f59ef7f9a4c2ddb0bff37ebbb';

// sql执行安全配置
$_config['security']['querysafe']['status'] = 1;
$_config['security']['querysafe']['dfunction']['0'] = 'load_file';
$_config['security']['querysafe']['dfunction']['1'] = 'hex';
//$_config['security']['querysafe']['dfunction']['2'] = 'substring';
$_config['security']['querysafe']['dfunction']['3'] = 'if';
$_config['security']['querysafe']['dfunction']['4'] = 'ord';
$_config['security']['querysafe']['dfunction']['5'] = 'char';
$_config['security']['querysafe']['daction']['0'] = 'intooutfile';
$_config['security']['querysafe']['daction']['1'] = 'intodumpfile';
$_config['security']['querysafe']['daction']['2'] = 'unionselect';
//$_config['security']['querysafe']['daction']['3'] = '(select';
$_config['security']['querysafe']['daction']['4'] = 'unionall';
$_config['security']['querysafe']['daction']['5'] = 'uniondistinct';
$_config['security']['querysafe']['daction']['6'] = '@';
$_config['security']['querysafe']['dnote']['0'] = '/*';
$_config['security']['querysafe']['dnote']['1'] = '*/';
$_config['security']['querysafe']['dnote']['2'] = '#';
$_config['security']['querysafe']['dnote']['3'] = '--';
$_config['security']['querysafe']['dnote']['4'] = '"';
$_config['security']['querysafe']['dlikehex'] = 1;
$_config['security']['querysafe']['afullnote'] = 1;

//FTP远程附件配置参数
$_config['FTP']['FTP_ON'] = false;//启用远程附件, true or false
$_config['FTP']['FTP_SSL'] = false;//启用 SSL 连接 true or fals
$_config['FTP']['FTP_HOST'] = '192.168.200.28';//FTP 服务器地址
$_config['FTP']['FTP_PORT'] = '21';//FTP 服务器端口
$_config['FTP']['FTP_USER'] = 'ftpupload';//FTP 帐号
$_config['FTP']['FTP_PASSWORD'] = 'careland';//FTP 密码
$_config['FTP']['FTP_PASVS'] = false;//被动模式(pasv)连接 true or false
$_config['FTP']['ATTACHDIR'] = '/sign/';//远程附件目录
$_config['FTP']['FTP_TIMEOUT'] = '0';//FTP 传输超时时间
$_config['FTP']['FTP_LOG'] = false;//开启操作日志 true or false
$_config['FTP']['ATTACH_URL'] = $_config['FTP']['FTP_ON'] ? '' : '';//远程访问 URL
?>