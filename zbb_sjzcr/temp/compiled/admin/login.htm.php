<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>壹深圳管理平台</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="Careland Inc." name="Copyright" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
<link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
<link rel="stylesheet" href="css/login.css" type="text/css" media="all" />
<link rel="stylesheet" href="css/weui.css" type="text/css" media="all" />
</head>
<body>
<div id="main-content">
	<div class="login-body">
		<div class="login-main pr">
			<form class="login-form" method="post" action="" onsubmit="return login();">
				<h3 class="welcome"><i class="login-logo"></i>壹深圳管理平台</h3>
				<div class="item-box" id="itemBox">
					<div class="item">
						<i class="icon-login-user"></i>
						<input type="text" autocomplete="off" placeholder="请填写用户名" name="admin_username">
					</div>
					<span class="placeholder_copy placeholder_un">请填写用户名</span>
					<div class="item b0">
						<i class="icon-login-pwd"></i>
						<input type="password" autocomplete="off" placeholder="请填写密码" name="admin_password">
					</div>
					<span class="placeholder_copy placeholder_pwd">请填写密码</span>
					<div class="item verifycode">
						<i class="icon-login-verifycode"></i>
						<input type="text" autocomplete="off" placeholder="请填写验证码" name="captcha">
						<a href="javascript:updateCaptcha();" title="换一张" class="reloadverify">换一张？</a>
					</div>
					<span class="placeholder_copy placeholder_check">请填写验证码</span>
					<div style="height:46px;">
						<img src="login.php?action=captcha&<?php echo $this->_var['random']; ?>" class="verifyimg reloadverify" alt="CAPTCHA" border="1" onclick='javascript:updateCaptcha();' style="cursor: pointer;" title="看不清？点击更换" />
					</div>
				</div>
				<div class="login_btn_panel">
					<button type="submit" class="login-btn">
						<span class="on">登 录</span>
					</button>
					<div class="check-tips"></div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.js,common.js,footer.js,login_api.js')); ?>
<script>	
	jQuery(".item input").focus(function(){
		jQuery(this).parent().addClass("focus");
	}).blur(function(){
		jQuery(this).parent().removeClass("focus");
	});
</script>
</body>
</html>