//微信分享
wx.config({
	//debug: true,
	appId: signPackage.appId,
	timestamp: signPackage.timestamp,
	nonceStr: signPackage.nonceStr,
	signature: signPackage.signature,
	jsApiList: [
		'onMenuShareTimeline',
		'onMenuShareAppMessage'
	]
});
wx.ready(function(){
	var title = "深广电十佳主持人 需要你来支持";
	var desc = "为你喜欢的主持人投票";
	var link = "http://static.scms.sztv.com.cn/ymq_h5/2018hd/zbb_sjzcr/do.php";
	var imgurl = "http://static.scms.sztv.com.cn/ymq_h5/2018hd/zbb_sjzcr/images/share.jpg?t=2022";
	wx.onMenuShareAppMessage({
	   title: title,
	   desc: desc,
	   link: link,
	   imgUrl: imgurl,
		trigger: function (res) {
		//alert('用户点击发送给朋友');
		},
		success: function (res) {
		
		}
	});
	wx.onMenuShareTimeline({
		  title: title,
		  desc: desc,
		  link: link,
		  imgUrl: imgurl,
		  trigger: function (res) {
			  	var zhuanfa = getCookie('zhuanfa');
				if(zhuanfa != '1'){
					setCookie('zhuanfa','1');
					setCookie('haszhuan','1');
				}
		  },
		  success: function (res) {
		  },
		  cancel: function (res) {
			//alert('已取消');
		  },
		  fail: function (res) {
			//alert(res.errMsg);
		  }
	});
});
try{
	var _hmt = _hmt || []; 
	var _sTime = new Date();
	(function() { 
	var sdk = document.createElement("script");
	sdk.src = "http://webjsbd.sztv.com.cn:8000/js/h5_stat.js";
	var s = document.getElementsByTagName("script")[0]; 
	s.parentNode.insertBefore(sdk, s); 
	})();
}catch(err){}