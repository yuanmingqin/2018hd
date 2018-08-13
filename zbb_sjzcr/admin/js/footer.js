function isNull(data){ 
	return (data == "" || data == 'undefined' || data == null) ? true : false; 
}
function showDialog(title,content,url){
	jQuery(".weui_dialog_title").html(title);
	jQuery(".weui_dialog_bd").html(content);
	jQuery(".weui_btn_dialog").attr('onclick','closeDialog(\''+url+'\')');
	jQuery("#dialog2").show();
}
function closeDialog(url){
	jQuery("#dialog2").hide();
	if(!isNull(url)){
		window.location.href = url;
	}
}
function loadDialog(){
	var html = '<div style="display:none;" id="dialog2" class="weui_dialog_showDialog"><div class="weui_mask"></div><div class="weui_dialog"><div class="weui_dialog_hd"  style="text-align:center"><strong class="weui_dialog_title"></strong></div><div class="weui_dialog_bd" style="text-align:center"></div><div class="weui_dialog_ft"><a class="weui_btn_dialog primary" href="javascript:;" onclick="" style="text-align:center">确定</a></div></div></div>';
	jQuery('body').prepend(html);
}
loadDialog();