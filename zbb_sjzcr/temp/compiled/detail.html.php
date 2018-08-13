<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<title>深广电十佳主持人 需要你来支持</title>
	<link rel="stylesheet" href="./css/detail.css?t=2019"/>
	<link rel="stylesheet" href="./css/weui_new.css?t=2017"/>
	<style>
	</style>
</head>
<body>
	<div class="share_layer"><img src="<?php echo $this->_var['imgroot']; ?>/images/share_layer.png"></div>
	<div class="return_img"><img src="<?php echo $this->_var['imgroot']; ?>/images/page2/return.png"></div>
	<div class="detail_bg">
		<img class="bg" src="<?php echo $this->_var['imgroot']; ?>/images/page2/bg.jpg" width="100%" height="100%">
	</div>
	<div class="main_bg">
		<div class="main_table_bg">
			<table cellspacing="0" cellpadding="0" class="main_table">
				<tr>
					<td class="xuhao_bg">
						<?php echo $this->_var['info']['xuhao']; ?>&nbsp;号
					</td>
					<td align="right" class="poll_num">
						<img src="<?php echo $this->_var['imgroot']; ?>/images/page2/love.png" width="15" align="absmiddle">&nbsp;&nbsp;票数：<span id="poll_num_span"><?php echo $this->_var['info']['poll_num']; ?></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center" class="avatar">
						<img src="./<?php echo $this->_var['info']['img']; ?>" width="70%">
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<div class="name"><?php echo $this->_var['info']['name']; ?></div>
						<div class="detail"><?php echo $this->_var['info']['detail']; ?></div>
					</td>
				</tr>
			</table>
		</div>
		<div class="lp_vote">
			<table cellspacing="0" cellpadding="0" class="lp_vote_table">
				<tr>
					<td class="lp" style="padding-left:2%;"><img width="95%" src="<?php echo $this->_var['imgroot']; ?>/images/page2/lp.png"/></td>
					<td class="vote" style="padding-right:0%;"><img width="95%"  src="<?php echo $this->_var['imgroot']; ?>/images/page2/vote.png"/></td>
				</tr>
			</table>
		</div>
	</div>
	<script type="text/javascript" src="./js/jquery-2.1.3.min.js"></script>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<script type="text/javascript">
	</script>
	<script type="text/javascript">
		var signPackage = <?php echo $this->_var['signPackage']; ?>;
		var imgroot = '<?php echo $this->_var['imgroot']; ?>';
		var name = '<?php echo $this->_var['name']; ?>';
		var user_group = '<?php echo $this->_var['user_group']; ?>';
		var id = '<?php echo $this->_var['id']; ?>'
	</script>
	<script src="./js/common.js?t=<?php echo $this->_var['time']; ?>"></script>
	<script src="./js/detail.js?t=3021"></script>
	<script src="./js/share_status_detail.js?t=20199"></script>
</body>
</html>