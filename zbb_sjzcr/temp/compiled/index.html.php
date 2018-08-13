<!DOCTYPE>
<html>
<head lang="zh-cn">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>深广电十佳主持人 需要你来支持</title>
	<link rel="stylesheet" href="./css/myAnimate.css?t=<?php echo $this->_var['time']; ?>"/>
	<link rel="stylesheet" href="./css/swiper.min.css">	
	<link rel="stylesheet" href="./css/pagination.css?t=<?php echo $this->_var['time']; ?>"/>
	<link rel="stylesheet" href="./css/index.css?t=19099"/>
	<link rel="stylesheet" href="./css/weui_new.css?t=2017"/>
	<script src="./js/jquery-2.1.3.min.js"></script>
	<script src="./js/jroll.min.js"></script>
	<script src="./js/swiper.js"></script>
	<script src="./js/jquery.pagination.js"></script>
	<script>
	var signPackage = <?php echo $this->_var['signPackage']; ?>;
	var imgroot = '<?php echo $this->_var['imgroot']; ?>';
	var isClickVote = '<?php echo $this->_var['isClickVote']; ?>';
	</script>
</head>
<style>
</style>
<body style="font-family:SimHei">
<div class="share_layer"><img src="<?php echo $this->_var['imgroot']; ?>/images/share_layer.png"></div>
<div class="wrap swiper-container">
    <div class="swiper-wrapper">
		<div class="swiper-slide swiper-no-swiping" id="sec00">
			<div class="bg_floor">
				<img class="bg" width="100%" height="100%" src="<?php echo $this->_var['imgroot']; ?>/images/page0/bg.jpg?t=2023"/>
			</div>
			<div class="start_div">
				<div class="video_gif">
					<img class="bg" width="100%"  data-original="<?php echo $this->_var['imgroot']; ?>/images/page0/video.gif?t=2025"/>
				</div>
				<div class="logoText">
				</div>
			</div>
		</div>
		<div class="swiper-slide swiper-no-swiping" id="sec02" style="overflow-y:hidden;">
			<div id="wrapper">
				<div id="scroller">
					<div class="bgm-btn rotate">
						<audio id="Jaudio" src="<?php echo $this->_var['imgroot']; ?>/images/bg.mp3?t=2022" loop></audio>
					</div>
					<div class="bg_header"><img class="bg" width="100%" src="<?php echo $this->_var['imgroot']; ?>/images/page1/bg_header.jpg?t=2018"/></div>
					<div class="group_02" id="group_02">
						<table class="pv_count" width="100%" align="center">
							<tr>
								<td width="48%" align="right" style="padding-right:0px;">
									<div class="canyu_count">
										投票次数:<?php echo $this->_var['canyuCount']; ?>
									</div>
								</td>
								<td width="4%" align="center" style="text-align:center;"> 
									<span class="line">
										&nbsp;
									</span>
								</td>
								<td width="48%" align="left">
									<div class="click_count">
										访问量:<?php echo $this->_var['clickCount']; ?>
									</div>
								</td>

							</tr>
						</table>
						<div class="group_search">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<input class="group_input keywords" type="text" value="" style="text-align:left;color:#000" placeholder="输入主持人名称"/>
								</td>
								<td class="group_search_button">
									<span class="group_search_span">
										<img class="bg" width="100%" height="100%" src="<?php echo $this->_var['imgroot']; ?>/images/page1/group_search_button.png?t=2023"/>
									</span>
								</td>
							</tr>
							</table>
						</div>
						<div class="type_tab">
							<table width="60%" align="center">
								<tr>
									<td class="tv select_type" id="tv" title="1">
										<img  class="bg" width="100%" src="<?php echo $this->_var['imgroot']; ?>/images/page1/tv_on.png?t=2025"/>
									</td>
									<td class="tr select_type" id="tr" title="2">
										<img  class="bg" width="100%" src="<?php echo $this->_var['imgroot']; ?>/images/page1/br.png?t=2025"/>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="bg_main">
						<div><img class="bg_main_bg" class="bg" width="100%" src="<?php echo $this->_var['imgroot']; ?>/images/page1/bg_main.jpg?t=2026"/></div>
					</div>
					<div class="player_list">
						<table class="player_table" width="90%" align="center" style="margin:0 auto;">
						</table>
						<div class="M-box"></div>
					</div>						
				</div>
			</div>
		</div>
    </div>
</div>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script src="./js/common.js?t=<?php echo $this->_var['time']; ?>"></script>
<script src="./js/index.js?t=19090"></script>
<script type="text/JavaScript">
</script>
<script src="./js/share_status.js?t=<?php echo $this->_var['time']; ?>"></script>			
</body>
</html>