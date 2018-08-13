function updateCaptcha(){
	 jQuery(".verifyimg").attr("src","login.php?action=captcha&"+Math.random());
}
function login(){	
	var admin_username = jQuery('input[name="admin_username"]').val();
	var admin_password = jQuery('input[name="admin_password"]').val();
	var captcha = jQuery('input[name="captcha"]').val();
	var para = {
		admin_username:admin_username,
		admin_password:admin_password,
		captcha:captcha
	}
	jQuery.ajax({
		cache: false,
		type: "POST",
		url:jsPath+"../API/login_api.php?ac=login",
		data:para,
		dataType:'json',
		async: false,
		success: function(result) {
			if(result && result.errcode == 0){
				//showDialog('操作成功','','index.php');
				window.location.href = "index.php";
			}else{
				jQuery(".check-tips").html(result.errmsg);
				updateCaptcha();
				//showDialog(result.errmsg);
			}
		}
	});
	return false;
}