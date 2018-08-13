function checkMember(frm){	
	var user_name = jQuery('input[name="user_name"]').val();
	if(!user_name){
		showDialog('用户名不能为空');
		return false;
	}
	var todolist =[];
	jQuery('input[name="todolist[]"]:checked').each(function(){
		todolist.push(jQuery(this).val());
	}); 
	if(todolist.length == 0){
		showDialog('设置权限不能为空');
		return false;
	}
	var password = jQuery('input[name="password"]').val();
	if(operation == 'add'){
		if(!password){
			showDialog('密码不能为空');
			return false;
		}
		var para = {
			'user_name':user_name,
			'todolist':todolist,
			'password':password
		};
		jQuery.ajax({
				cache: false,
				type: "POST",
				url:jsPath+"../API/member_api.php?ac=add",
				data:para,
				dataType:'json',
				async: false,
				success: function(result) {
					if(result && result.errcode == 0){
						showDialog('操作成功','','index.php?action=member');
					}else{
						showDialog(result.errmsg);
					}
				}
		});
	}else{
		var admin_id = jQuery('input[name="admin_id"]').val();
		var newpassword = jQuery('input[name="newpassword"]').val();
		var para = {
			'user_name':user_name,
			'todolist':todolist,
			'password':password,
			'newpassword':newpassword,
			'admin_id':admin_id
		};
		
		jQuery.ajax({
				cache: false,
				type: "POST",
				url:jsPath+"../API/member_api.php?ac=edituser",
				data:para,
				dataType:'json',
				async: false,
				success: function(result) {
					if(result && result.errcode == 0){
						showDialog('操作成功','','index.php?action=member');
					}else{
						showDialog(result.errmsg);
					}
				}
		});
	}
	return false;
}
function editpass(){
	var admin_id = jQuery('input[name="admin_id"]').val();
	var password = jQuery('input[name="password"]').val();
	var newpassword = jQuery('input[name="newpassword"]').val();
	if(!admin_id){
		showDialog("非法操作");
		return false;
	}
	if(!password || !newpassword){
		showDialog("新旧密码不能为空");
		return false;
	}
	var para  = {
		admin_id:admin_id,
		password:password,
		newpassword:newpassword
	}
	jQuery.ajax({
		cache: false,
		type: "POST",
		url:jsPath+"../API/member_api.php?ac=editpass",
		data:para,
		dataType:'json',
		async: false,
		success: function(result) {
			if(result && result.errcode == 0){
				showDialog('操作成功','','index.php?action=member');
			}else{
				showDialog(result.errmsg);
			}
		}
	});
	return false;
}
function del(obj){
	var id = obj.id;
	var para  = {
		id:id
	}
	jQuery.ajax({
		cache: false,
		type: "POST",
		url:jsPath+"../API/member_api.php?ac=del",
		data:para,
		dataType:'json',
		async: false,
		success: function(result) {
			if(result && result.errcode == 0){
				showDialog('操作成功','','index.php?action=member');
			}else{
				showDialog(result.errmsg);
			}
		}
	});
	return false;
}