(function () { 
	function vote(){
		var para = {
			id:id,
			user_group:user_group
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
					var poll_num = parseInt($("#poll_num_span").html())+1;
					$("#poll_num_span").html(poll_num);
				}else{
					showDialog(json.errmsg);
				}
			}
		});
	}
	var w_width = $('body').width();
	var w_height = $(window).height();
	//$('.detail_bg img').css({'height':w_height+"px"});
	$('.main_table_bg').css({'width':0.7*w_width+"px",'height':0.7*w_width*1.5+"px"});
	$('.detail p,.detail span').css({'fontSize':'14px'});
	$(".lp").on('click touchstart',function(e) {
		setTimeout(function(){
			$('.share_layer').fadeIn();
			
		},200);
		e.stopPropagation();				
	});
	$(".share_layer").on('click',function(e) {
		$('.share_layer').fadeOut();
		e.stopPropagation(); 
	});
	$(".vote").on('click',function(e) {
		vote();
	});
	$('.return_img').on('click',function(e) {
		var ua = window.navigator.userAgent.toLowerCase();
		//通过正则表达式匹配ua中是否含有MicroMessenger字符串
		if(ua.match(/MicroMessenger/i) == 'micromessenger'){
			window.location.href = "index.php";
		}else{
			window.location.href = "index_app.php";
		}
	});
})(jQuery);