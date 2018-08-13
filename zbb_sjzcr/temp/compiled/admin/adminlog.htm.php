<?php echo $this->fetch('header.htm'); ?>
<h3>管理员日志</h3>
<?php if ($this->_var['operation'] == 'list'): ?>
	<div class="search-div" style="margin-bottom:5px;">
		<form name="searchForm" action="index.php?action=adminlog&op=list" method="post">
			<table style="width:auto;margin:5px;" cellpadding="2">
				<tr>
					<td>管理员：</td>
					<td>
						<select id="adminlist" name="adminlist">
						  <option value="" selected="">请选择...</option>
						  <?php $_from = $this->_var['admin_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'admin');if (count($_from)):
    foreach ($_from AS $this->_var['admin']):
?>
						  <option value="<?php echo $this->_var['admin']['admin_id']; ?>"><?php echo $this->_var['admin']['user_name']; ?></option>
						  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						</select>
					</td>
					<td>&nbsp;&nbsp;</td>
					<td><input type="text" size="15" id="key" name="key" placeholder="输入关键字"></td>
					<td><input type="submit" class="button" value=" 搜索 "> </td>
				</tr>
			</table>
		</form>
	</div>
	<div class="list-div" id="listDiv">
		<table cellspacing='1' cellpadding='3'>
		  <tr>
			<th>日志ID</th>
			<th>操作者</th>
			<th>操作日期</th>
			<th>ip地址</th>
			<th>操作记录</th>
		  </tr>
		  <?php $_from = $this->_var['log_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'log');if (count($_from)):
    foreach ($_from AS $this->_var['log']):
?>
		  <tr>
			<td><?php echo $this->_var['log']['log_id']; ?></td>
			<td><?php if ($this->_var['log']['user_name']): ?><?php echo $this->_var['log']['user_name']; ?><?php else: ?>已删除(<?php echo $this->_var['log']['adminid']; ?>)<?php endif; ?></td>
			<td><?php echo $this->_var['log']['log_time']; ?></td>
			<td><?php echo $this->_var['log']['ip_address']; ?></td>
			<td><?php echo $this->_var['log']['log_info']; ?></td>
		  </tr>
		  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</table>
		<div class="pages"><?php echo $this->_var['multi']; ?></div>
	</div>
<?php endif; ?>
<?php echo $this->fetch('footer.htm'); ?>