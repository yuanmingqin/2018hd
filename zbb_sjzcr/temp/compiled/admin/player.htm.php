<?php echo $this->fetch('header.htm'); ?>
<script type="text/javascript" charset="utf-8" src="./Ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="./Ueditor/ueditor.all.min.js"> </script>


<script type="text/javascript" charset="utf-8" src="./Ueditor/lang/zh-cn/zh-cn.js"></script>
<?php if ($this->_var['operation'] == 'list'): ?>
<h3>投票管理 - 选手列表</h3>
	<div class="list-div" id="listDiv">
	<table cellspacing='1' cellpadding='3'>
	  <tr class="theader">
		<th>用户</th>
		<th>编号</th>
		<th>简介</th>
		<th>票数</th>
		<th>所属分组</th>
		<th>操作</th>
	  </tr>
	  <?php $_from = $this->_var['player']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
	  	 <tr >
			<td class="first-cell">
			<img align="absmiddle" src="<?php echo $this->_var['imgroot']; ?><?php echo $this->_var['item']['img']; ?>" width="100">&nbsp;<?php echo $this->_var['item']['name']; ?>
			</td>
			<td><?php echo $this->_var['item']['xuhao']; ?></td>
			<td><?php echo $this->_var['item']['desc']; ?></td>
			<td><?php echo $this->_var['item']['poll_num']; ?></td>
			<td><?php echo $this->_var['item']['group_name']; ?></td>
			<td>
				<a id="<?php echo $this->_var['item']['id']; ?>" href="javascript:;" onclick="del(this)">删除</a>
				<a href="index.php?action=player&op=edit&id=<?php echo $this->_var['item']['id']; ?>&page=<?php echo $this->_var['page']; ?>">编辑</a>
			</td>
		</tr>
	  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</table>
	</div>
	<div class="pages"><?php echo $this->_var['multi']; ?></div>
	<div class="main-div">
		<input type="button" class="button" value="添加选手" onclick="javascript:location.href='index.php?action=player&op=add'" />
	</div>

<?php elseif ($this->_var['operation'] == 'add' || $this->_var['operation'] == 'edit'): ?>
<h3><?php if ($this->_var['operation'] == 'add'): ?>添加<?php else: ?>编辑<?php endif; ?>选手</h3>
	<form action="index.php?action=player&op=<?php echo $this->_var['operation']; ?>&page=<?php echo $this->_var['page']; ?>" method="post" onsubmit="return checkplayer(this)" enctype="multipart/form-data">
		<div class="edit-div list-div-detail">
		<table cellspacing="1" cellpadding="3" width="100%">
		  <tr>
			<th width="80"><span style="color:red;">*</span>名字</th>
			<td><input style="height:30px;" type="text" name="name" value="<?php echo $this->_var['info']['name']; ?>" size="120"/></td>
			</tr>
			<tr>
					<th width="80">编号</th>
					<td><input style="height:30px;" type="text" name="xuhao" value="<?php echo $this->_var['info']['xuhao']; ?>" size="120"/></td>
			</tr>
			<tr>
				<th width="80">票数</th>
				<td><input style="height:30px;" type="text" name="poll_num" value="<?php echo $this->_var['info']['poll_num']; ?>" size="120"/></td>
			</tr>
			<tr>
				<th width="80">简介</th>
				<td><input style="height:30px;" type="text" name="desc" value="<?php echo $this->_var['info']['desc']; ?>" size="120"/></td>
			</tr>
		  <tr style="height:150px;">
			<th>头像</th>
			<td>
				<input type="file" name="img"/>
				<?php if ($this->_var['info']['img']): ?>
					<img src="<?php echo $this->_var['imgroot']; ?>/<?php echo $this->_var['info']['img']; ?>" width="150" height="150"/>
				<?php endif; ?>
			</td>
			</tr>
			<tr>
				<th>详细介绍</th>
				<td><textarea name="detail" id="detail"><?php echo $this->_var['info']['detail']; ?></textarea>
					<script type="text/javascript">
						var detail_editor = new UE.ui.Editor({initialFrameHeight:300,initialFrameWidth:800}); 
						detail_editor.render("detail");
					</script>
				</td>
			</tr>
		  <tr style="display:none;">
			<th><span style="color:red;">*</span>所属分组</th>
			<td>
				<select name="user_group">
				<?php $_from = $this->_var['user_GROUP']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
					<option value="<?php echo $this->_var['key']; ?>" <?php if ($this->_var['info']['user_group'] == $this->_var['key']): ?>selected<?php endif; ?>><?php echo $this->_var['item']; ?></option>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				</select>
			</td>
		  </tr>
		  <tr align="center">
			<td colspan="2">
			  <input type="hidden"  name="id"       value="<?php echo $this->_var['info']['id']; ?>" />
			  <input type="submit" class="button" name="Submit"       value="确定" />
			  <input type="reset" class="button"  name="Reset"        value="重置" />
			</td>
		  </tr>
		</table>
		</div>
	</form>
<?php endif; ?>
<script type="text/javascript">
var page = <?php echo $this->_var['page']; ?>;
</script>
<?php echo $this->smarty_insert_scripts(array('files'=>'footer.js,player_api.js')); ?>
<?php echo $this->fetch('footer.htm'); ?>