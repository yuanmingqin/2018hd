var index = 0;
var hash = window.location.hash;
if(hash == "#1"){
	index = 1;
}
var u = navigator.userAgent;
var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
w_height = $(window).height();
w_width = $('body').width();
bg_height = 1344;
bg_width = 750;
w_rate = w_width/bg_width;
h_rate = w_height/bg_height;
var user_group = 1;
var mySwiper,jroll;
function audioAutoPlay(id){  
	var audio = document.getElementById(id);
	if(audio.paused){
		audio.play(); 			
		document.addEventListener("WeixinJSBridgeReady", function () {  
				audio.play();  
		}, false);  
		document.addEventListener('YixinJSBridgeReady', function() {  
			audio.play();  
		}, false);
	}				
}
function audioAutoStop(id){  
	var audio = document.getElementById(id);
	if(!audio.paused){
		audio.pause(); 			
		document.addEventListener("WeixinJSBridgeReady", function () {  
				audio.pause();  
		}, false);  
		document.addEventListener('YixinJSBridgeReady', function() {  
			audio.pause();  
		}, false);
	}				
}
function ini(){
	window.localStorage.setItem('user_group',user_group);
	//audioAutoPlay('Jaudio');
	//音乐
	$(".bgm-btn").on('touchstart click',function(){
		var playing = $(this).hasClass('rotate');
		setTimeout(function() {
			if(playing){
				$(".bgm-btn").removeClass('rotate');
				audioAutoStop('Jaudio');
			}else{
				$(".bgm-btn").addClass('rotate');
				audioAutoPlay('Jaudio');
			}
		}, 500);
	});
	$('.swiper-container,#wrapper').css({'height':w_height+"px",'width':w_width+"px"});
	mySwiper = new Swiper('.swiper-container', {
		//direction: 'vertical',
		initialSlide:index,
		on:{
		  init: function(){
			//swiperAnimateCache(this); //隐藏动画元素 
			//swiperAnimate(this); //初始化完成开始动画
			this.allowSlidePrev= false;
		  }, 
		  slideChangeTransitionEnd: function(swiper){ 
			//swiperAnimateCache(this); //隐藏动画元素
			//swiperAnimate(this);
		  },	  
		}
	});
	var person_left = 38*w_rate;
	var persion_height= w_width/1.5/2.6;
	var persion_width = w_width/1.5;
	var persion_height_top = (w_height-persion_height)/2;
	//进度条
	$('.start_div').css({'top':h_rate*0.54*bg_height+"px"});
	$('.progress').css({'height':'200px','marginTop':'-100px','width':w_width*0.8+"px",'marginLeft':-(w_width*0.8/2)+"px"});
	$('.jindu_person').css({'marginLeft':-persion_width/2+"px",'marginTop':-persion_height/2+"px","left":"60%","width":persion_width+"px",'height':persion_height+"px","top":"50%"});
	$('.jindu_person img').css({'width':persion_width+"px",'height':persion_height+"px"});
	//第二页
	//
	var group_top  = w_rate*430;
	$('.group_02').css({'top':group_top+"px"});
	var group_search_button_width = 0.285*w_width;
	var group_search_button_height = 0.16*0.48*w_width;
	$('.group_search_button,.group_search_span').css({'width':group_search_button_width+"px",'height':group_search_button_height+2+"px"});
	$('.group_search_span').css({"marginLeft":-group_search_button_width+10+"px","marginTop":"-1px"});
	$('.group_search').css({'height':group_search_button_height+"px",'lineHeight':group_search_button_height+"px"});
	$('.group_input').css({'paddingRight':group_search_button_width+10+"px",'height':group_search_button_height+"px",'lineHeight':group_search_button_height+"px"});
	//去关注公众号回来刷新
	if(index == '1'){
		audioAutoPlay('Jaudio');
		second_ini();
	}else{
		loadImg();
	}

}
function first_ini(){
	audioAutoPlay('Jaudio');
	window.location.hash = "#1";
	mySwiper.slideTo(1, 1000, true);
	second_ini();
}
function second_ini(){
	w_height = $(window).height();
	w_width = $('body').width();
	w_rate = w_width/750;
	//var bg_2_height = 5202*w_rate;
	//滚动
	//$("#scroller").css({"maxHeight":bg_2_height+"px"});
	jroll = new JRoll('#wrapper',{
		scrollX: false,
		momentum:true,
		bounce:false,	
		scrollY: true,
		scrollBarY:true,
		scrollBarFade:true
	});
	getPlayerList(user_group,1,'',true);
	$(".group_search_span").on('click touchstart',function(e) {
		var keywords =  $(".keywords").val();
		getPlayerList(user_group,1,keywords,true);
	});
	$(".select_type").on('click touchstart',function(e) {
		var id = $(this).attr('id');
		if(id == 'tr'){
			user_group = 2;
			window.localStorage.setItem('user_group',2);
			$('#tv img').attr('src',imgroot+"/images/page1/tv.png?t=2017");
			$('#tr img').attr('src',imgroot+"/images/page1/br_on.png?t=2017");
			getPlayerList(2,1,'',true);
		}else{
			user_group = 1;
			window.localStorage.setItem('user_group',1);
			$('#tv img').attr('src',imgroot+"/images/page1/tv_on.png?t=2017");
			$('#tr img').attr('src',imgroot+"/images/page1/br.png?t=2017");
			getPlayerList(1,1,'',true);
		}
	});
}
function vote(id,user_group){
	var para = {
		id:id,
		user_group:user_group,
	}
	$.ajax({
		type : "POST",
		async : false,
		data:para,
		url : "./API/init.php?ac=vote",
		dataType : "json",
		jsonp : "callback",
		success: function(json){
			if(json.errcode == 0){
				//var note = "投票成功！"
				showDialog("投票成功！");
				var poll_num = parseInt($("#poll_num_"+id).html())+1;
				$("#poll_num_"+id).html(poll_num);
			}else{
				showDialog(json.errmsg);
			}
		}
	});
}
function getPlayerList(user_group,page,keywords,first){
	var para = {
		user_group:user_group,
		page:page,
		keywords:keywords
	}
	$.ajax({
		type : "GET",
		async : false,
		data:para,
		url : "./API/init.php?ac=getPlayerList",
		dataType : "json",
		jsonp : "callback",
		success: function(json){
			var pageCount = json.data.pageCount;
			var data = json.data.res;
			var html = "";
			var j = 0;
			for(i=0;i<data.length;i++){
				var item = data[i];
				if(item){
					j++;
					html += '<tr class="player_total_tr"><td class="player_info" align="center" valign="top"><table width="100%"><tr><td class="player_no">'+item.xuhao+'号</td><td align="right" class="love" colspan="2"><img  src="./images/page1/love.png" align="absmiddle"/>&nbsp;票数:&nbsp;<span class="poll_num" id="poll_num_'+item.id+'">'+item.poll_num+'</span></td></tr><tr class="player_tr"><td width="30%" class="player_avatar"><img  class="lazy" src="./'+item.img+'"/></td><td width="45%" valign="top"><div class="player_name">'+item.name+'</div><div class="player_desc">'+item.desc+'</div><div class="player_detail" title="'+item.id+'"><img  src="./images/page1/detail.png"/></div></td><td><div class="player_lp" title="'+item.id+'"><img  src="./images/page1/lp.png"/></div><div class="player_vote" title="'+item.id+'"><img  src="./images/page1/vote.png?t=2018"/></div></td></tr></table></td></tr>';
				}			
			}		
			
			//选手大背景
			var bg_main_top = w_rate*674;
			var bg_main_width = w_width;
			var bg_main_height = '';
			if(j==0){
	            bg_main_height = w_height-$(".bg_header").height(); 
				html = "<tr><td align='center' style='color:#fff;margin:10px 0;'>无相关信息</td></tr>";
			}else{
				if(isAndroid){
					bg_main_height = 170*j+380*w_rate;
				}else{
					bg_main_height = 170*j+280;
				}	
			}
			$(".player_table").html(html);
			
			$('.bg_main_bg').css({'width':bg_main_width+"px",'height':bg_main_height+"px"});
			$(".player_list").css({'top':bg_main_top+"px"});
			//选手小背景
			var player_info_width = 0.917*w_width;
			var player_info_height = 150;
			$(".player_info").css({'width':player_info_width+"px",'height':player_info_height+"px"});
			//选手编号背景
			var player_no_width = 0.157*w_width;
			var player_no_height = 0.157*w_width*0.446;
			$(".player_no").css({'width':player_no_width+"px",'height':player_no_height+"px",'lineHeight':player_no_height+"px"});
			//投票前爱心图标
			var love_width = 0.042*w_width;
			$(".love img").css({'width':love_width+"px"});
			//详细介绍
			var player_detail_width = 0.174*w_width;
			$(".player_detail img").css({'width':player_detail_width+"px"});
			//选手头像
			var player_avatar_width = 0.224*w_width;
			var player_avatar_height = 0.224*w_width;
			$(".player_avatar img").css({'width':player_avatar_width+"px",'height':player_avatar_height+"px"});
			//选手名称
			$(".player_name").css({'width':player_avatar_width+"px"});
			//拉票投票按钮
			var lp_vote_width = 0.165*w_width;
			$(".player_lp,.player_vote").css({'width':lp_vote_width+"px"});
			$(".player_lp").on('click',function(e) {
				//setTimeout(function(){
					//$('.share_layer').fadeIn();
					
				//},200);
				var id = $(this).attr('title');
				window.location.href = "detail.php?id="+id;
				//e.stopPropagation();				
			});
			$(".share_layer").on('click',function(e) {
				$('.share_layer').fadeOut();
				//e.stopPropagation(); 
			});
			$(".player_vote").on('click',function(e) {
				//setTimeout(function(){
				var id = $(this).attr('title');
				user_group = window.localStorage.getItem('user_group');
				vote(id,user_group);
				//},200);
			});
			$(".player_detail").on('click',function(e) {
				var id = $(this).attr('title');
				window.location.href = "detail.php?id="+id;
			});
			fenye(user_group,pageCount,page,keywords,first);
		}
	});
}
function fenye(user_group,pageCount,page,keywords,first){
	$('.M-box').pagination({
		pageCount:pageCount,
		jump:false,
		current:page,
		count:1,
		coping:false,
		callback:function(index){
			var page = index.getCurrent();
			getPlayerList(user_group,page,keywords,false);
		}
	});
	$('.M-box').show();
	if(isNull(first)){
		jroll.scrollToElement("#group_02",1000);
	}
	jroll.refresh();
}
function loadImg(){
	var imgdata = [
		'images/page2/bg.jpg',
		'images/page1/bg_header.jpg',
		'images/page1/bg_main.jpg',
		'images/share.jpg'
	];
	var nub = 0;
	var logoText = document.querySelector('.logoText');
	for (var i = 0; i < imgdata.length; i++) {
		(function(e) {
			setTimeout(function(){
				var img = new Image();
				img.src = imgdata[e];
				img.onload = function() {
					nub++;
					$('.progressbar').css('width', (Math.floor(nub / imgdata.length * 100))+'%');
					logoText.innerHTML = "正在加载 " + (Math.floor(nub / imgdata.length * 100)) + "%";
					if(nub==1){
						$(".video_gif img").attr("src",$(".video_gif img").attr('data-original'));
					}
					if (nub == imgdata.length) {
						var html = "<div class='start_button'><img id='start' src='./images/page0/start.png?t=2019' type='2'/></div>"
						$(".logoText").html(html).fadeIn();
						$('.start_button').on('click touchstart',function(e) {	
							first_ini();
						});
						//$(".logoText").hide();	
					}
				}
			},200*(e+1));
		})(i);
	}
}
$(function(){
	ini();
}); 	