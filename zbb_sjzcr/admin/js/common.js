//后台管理菜单js函数
//添加Cookie
var js=document.scripts;
var jsPath;
for(var i=0;i<js.length;i++){
 if(js[i].src.indexOf("common.js")>-1){
   jsPath=js[i].src.substring(0,js[i].src.lastIndexOf("/")+1);
 }
}
function addCookie(cookieName, cookieValue, cookieDay){//添加cookie
	var str = cookieName + "=" + escape(cookieValue);
	if (cookieDay > 0) {//为0时不设定过期时间，浏览器关闭时cookie自动消失
		var date = new Date();
		var ms = cookieDay * 24 * 3600 * 1000;
		date.setTime(date.getTime() + ms);
		str += ";expires=" + date.toGMTString();
	}
	document.cookie = str;
}

//更新设置一个Cookie
function setCookie(cookieName, cookieValue){
	var value = escape(cookieValue);
	if (value.length < 3000) {//3000最高限度值
		var date = new Date();
		var ms = 7 * 24 * 3600 * 1000;
		date.setTime(date.getTime() + ms);
		document.cookie = cookieName + "=" + value + "; expires=" + date.toGMTString();//+";path=/;domain=.kldjy.com";
	} else {
		var templist = unescape(value).split("|");
		templist.splice(0, 1);
		value = templist.join("|");
		setCookie(cookieName, value);
	}
}

//获取指定名称的cookie的值
function getCookie(cookieName){
	var arrStr = document.cookie.split("; ");
	for (var i = 0; i < arrStr.length; i++) {
		var temp = arrStr[i].split("=");
		if (temp[0] == cookieName) {
			return unescape(temp[1]);
		}
	}
}


function documentmenu() {
	//默认展开第1个菜单
	if(!getCookie('admin_menu_collapseid')){
		addCookie('admin_menu_collapseid', 0);
	}
	
	var returnstr = '';
	var currentfile = location.href.substr(location.href.lastIndexOf('/') + 1)+"&";
	
	returnstr = show_menu(menu, currentfile);
	document.write('<div class="side" style="height: 100%;">' + returnstr + '</div>');
}

function collapse(ctrlobj, menuid) {
	var submenu = jQuery(ctrlobj).parents(".onemenu").find("div.subinfo");			
	if(submenu){
		if(submenu[0].style.display == '') {
			addCookie('admin_menu_collapseid', -1);
			
			jQuery(ctrlobj).find("em").attr('class','spread');
			for (var i = 0; i < submenu.length; i++) {
				submenu[i].style.display = "none";		
			};
		} else {
			addCookie('admin_menu_collapseid', menuid);
			
			jQuery(ctrlobj).find("em").attr('class','shrink');
			for (var i = 0; i < submenu.length; i++) {
				submenu[i].style.display = "";		
			};
		}
	}	
}

function confirm_redirect(msg, url)
{
	if (confirm(msg)){
		location.href=url;
	}
}

function show_menu(menu, currentfile, showtype){
	var tabon='',returnstr='';
	for(var i in menu) {
		if(typeof(menu[i]['child']) == 'object') {
			returnstr +="<div class='onemenu'>";
			
			showtype = 0;
			if(getCookie('admin_menu_collapseid') == i.toString()){
				showtype = 1;
			}
			
			returnstr += '<a class="sideul" onclick="collapse(this,\''+i+'\');return false"';
			returnstr += '><em class="'+(showtype ? "shrink" : "spread")+'" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</em>' + menu[i]['title'] + '</a>';
			returnstr += show_menu(menu[i]['child'], currentfile, showtype);			
		} else {
			returnstr += '<div class="subinfo" style="display: '+(showtype ? "" : "none")+';">';
			tabon = '';
			
			if(currentfile.indexOf('action=') != -1 && currentfile.indexOf(menu[i]['url']+"&") != -1) {
				tabon = 'tabon ';
			}
			
			if(!menu[i]['url']) {
				menu[i]['url'] = '';
			}
			else{
				returnstr += '<a class="' + tabon + 'sidelist" href="' + menu[i]['url'] + '">' + menu[i]['title'] + '</a>';
			}
			
			returnstr += '</div>';
		}
		if(typeof(menu[i]['child']) == 'object') {
			returnstr +="</div>";
		}
	}
	return returnstr;
}