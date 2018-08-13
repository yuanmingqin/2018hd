function checkStore(frm){	
	var business_name = jQuery('input[name="business_name"]').val();
	if(!business_name){
		showDialog('店名不能为空');
		return false;
	}
	var tag = jQuery('input[name="tag"]').val();
	if(!tag){
		showDialog('标签名不能为空');
		return false;
	}
	var address = jQuery('input[name="address"]').val();
	var contacts = jQuery('input[name="contacts"]').val();
	if(operation == 'add'){
		var para = {
			'business_name':business_name,
			'tag':tag,
			'address':address,
			'contacts':contacts
		};
		jQuery.ajax({
				cache: false,
				type: "POST",
				url:jsPath+"../API/store_api.php?ac=add",
				data:para,
				dataType:'json',
				async: false,
				success: function(result) {
					if(result && result.errcode == 0){
						showDialog('操作成功','','index.php?action=store');
					}else{
						showDialog(result.errmsg);
					}
				}
		});
	}else{
		var id = jQuery('input[name="id"]').val();
		var pageindex = jQuery('input[name="pageindex"]').val();
		if(!id){
			showDialog('非法操作');
			return false;
		}
		var para = {
			'business_name':business_name,
			'tag':tag,
			'address':address,
			'contacts':contacts,
			'id':id
		};
		
		jQuery.ajax({
				cache: false,
				type: "POST",
				url:jsPath+"../API/store_api.php?ac=edit",
				data:para,
				dataType:'json',
				async: false,
				success: function(result) {
					if(result && result.errcode == 0){
						showDialog('操作成功','','index.php?action=store&page='+pageindex);
					}else{
						showDialog(result.errmsg);
					}
				}
		});
	}
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
		url:jsPath+"../API/store_api.php?ac=del",
		data:para,
		dataType:'json',
		async: false,
		success: function(result) {
			if(result && result.errcode == 0){
				showDialog('操作成功','','index.php?action=store');
			}else{
				showDialog(result.errmsg);
			}
		}
	});
	return false;
}