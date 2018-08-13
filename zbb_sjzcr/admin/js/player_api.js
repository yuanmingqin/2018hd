function checkUser(frm){	
	var name = jQuery('input[name="name"]').val();
	if(!name){
		showDialog('名字不能为空');
		return false;
	}
	return true;
}
function del(obj,type){
	var id = obj.id;
	var para  = {
		id:id
	}
	jQuery.ajax({
		cache: false,
		type: "POST",
		url:jsPath+"../API/player_api.php?ac=del",
		data:para,
		dataType:'json',
		async: false,
		success: function(result) {
			if(result && result.errcode == 0){
				if(type=='br'){
					showDialog('操作成功','','index.php?action=player_br&page='+page);
				}else{
					showDialog('操作成功','','index.php?action=player&page='+page);
				}
				
			}else{
				showDialog(result.errmsg);
			}
		}
	});
	return false;
}