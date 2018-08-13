var js=document.scripts;
jsPath = '';
for(var i=0;i<js.length;i++){
 if(js[i].src.indexOf("project.js")>-1){
   jsPath=js[i].src.substring(0,js[i].src.lastIndexOf("/")+1);
 }
}
rate = $('body').width()/640;
do_zoom = function(coords){
	var res = [];
	for(var i=0;i<coords.length;i++){
		res[i] = (coords[i]*rate).toFixed(2);
	}
	return res.join(",");
}
isNull = function(data){ 
	return (data == "" || data == 'undefined' || data == null) ? true : false; 
}
showDialog = function(content,url){
	$(".weui-dialog__bd").html(content);
	$(".weui-dialog__btn").attr('onclick','closeDialog(\''+url+'\')');
	$("#iosDialog2").show();
}
function closeDialog(url){
	$("#iosDialog2").hide();
	if(!isNull(url)){
		window.location.href = url;
	}
}
function loadDialog(url){
	var html = '<div class="js_dialog" id="iosDialog2" style="display:none;"><div class="weui-mask" style="z-index:6000;"></div><div class="weui-dialog" style="z-index:6001;"><div class="weui-dialog__bd"></div><div class="weui-dialog__ft"><a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary">确定</a></div></div></div>';
	$('body').prepend(html);
}
loadDialog();
getCookie = function (name)
	{
		var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
		if(arr=document.cookie.match(reg))
		return unescape(arr[2]);
		else
		return null;
	}
delCookie = function (name)
	{
		var exp = new Date();
		exp.setTime(exp.getTime() - 1);
		var cval=getCookie(name);
		if(cval!=null)
		document.cookie= name + "="+cval+";expires="+exp.toGMTString();
	}
setCookie = function (name,value)
{
	var Days = 0.1;
	var exp = new Date();
	exp.setTime(exp.getTime() + Days*24*60*60*1000);
	document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString()+";path=/;domain=static.scms.sztv.com.cn";
} 
var base64EncodeChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
var base64DecodeChars = new Array(
　　-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
　　-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
　　-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63,
　　52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1,
　　-1,　0,　1,　2,　3,  4,　5,　6,　7,　8,　9, 10, 11, 12, 13, 14,
　　15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1,
　　-1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
　　41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1);
base64encode = function (str) {
　　var out, i, len;
　　var c1, c2, c3;
　　len = str.length;
　　i = 0;
　　out = "";
　　while(i < len) {
		c1 = str.charCodeAt(i++) & 0xff;
		if(i == len)
		{
		　　 out += base64EncodeChars.charAt(c1 >> 2);
		　　 out += base64EncodeChars.charAt((c1 & 0x3) << 4);
		　　 out += "==";
		　　 break;
		}
		c2 = str.charCodeAt(i++);
		if(i == len)
		{
		　　 out += base64EncodeChars.charAt(c1 >> 2);
		　　 out += base64EncodeChars.charAt(((c1 & 0x3)<< 4) | ((c2 & 0xF0) >> 4));
		　　 out += base64EncodeChars.charAt((c2 & 0xF) << 2);
		　　 out += "=";
		　　 break;
		}
		c3 = str.charCodeAt(i++);
		out += base64EncodeChars.charAt(c1 >> 2);
		out += base64EncodeChars.charAt(((c1 & 0x3)<< 4) | ((c2 & 0xF0) >> 4));
		out += base64EncodeChars.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >>6));
		out += base64EncodeChars.charAt(c3 & 0x3F);
　　}
　　return out;
}