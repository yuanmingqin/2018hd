<?php echo $this->fetch('header.htm'); ?>
<?php if ($this->_var['operation'] == 'list'): ?>
<h3>权限管理 - 管理员列表</h3>
	<div class="list-div" id="listDiv">
	<table cellspacing='1' cellpadding='3'>
	  <tr class="theader">
		<th>用户名</th>
		<th>最近登陆IP</th>
		<th>最近登陆时间</th>
		<th>加入时间</th>
		<th>操作</th>
	  </tr>
	  <?php $_from = $this->_var['memberlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'member');if (count($_from)):
    foreach ($_from AS $this->_var['member']):
?>
	  	 <tr >
			<td class="first-cell"><?php echo $this->_var['member']['user_name']; ?></td>
			<td><?php echo $this->_var['member']['last_ip']; ?></td>
			<td><?php echo $this->_var['member']['last_login']; ?></td>
			<td><?php echo $this->_var['member']['add_time']; ?></td>
			<td>
				<a id="<?php echo $this->_var['member']['admin_id']; ?>" href="javascript:;" onclick="del(this)">删除</a>
				<a href="index.php?action=member&op=edituser&id=<?php echo $this->_var['member']['admin_id']; ?>">编辑</a>
			</td>
		</tr>
	  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</table>
	</div>
	<div class="main-div">
		<input type="button" class="button" value="添加管理员" onclick="javascript:location.href='index.php?action=member&op=add'" />
	</div>
<?php elseif ($this->_var['operation'] == 'edituser'): ?>
<h3>编辑管理员</h3>
	<form action="index.php?action=member&op=edituser" method="post" onsubmit="return checkMember(this)">
		<div class="edit-div list-div-detail">
		<table cellspacing="1" cellpadding="3" width="100%">
		  <tr>
			<th>用户名</th>
			<td><input name="user_name" class="text" type="text" value="<?php echo $this->_var['member']['user_name']; ?>" disabled="true" size="60" /></td>
		  </tr>
		  <tr>
			<th>旧密码</th>
			<td><input id="password" class="text" name="password" type="password" value="" size="60" /></td>
		  </tr>
		  <tr>
			<th>新密码</th>
			<td><input id="newpassword" class="text" name="newpassword" type="password" value="" size="60" /></td>
		  </tr>
		  <tr>
			<th>权限</th>
			<td>
				<?php echo $this->_var['checkbox_str']; ?>
			</td>
		  </tr>
		  <tr align="center">
			<td colspan="2">
			  <input type="hidden"  name="admin_id"       value="<?php echo $this->_var['member']['admin_id']; ?>" />
			  <input type="submit" class="button" name="Submit"       value="确定" />
			  <input type="reset" class="button"  name="Reset"        value="重置" />
			</td>
		  </tr>
		</table>
		</div>
	</form>
<?php elseif ($this->_var['operation'] == 'editpass'): ?>
<h3>修改密码</h3>
	<form action="index.php?action=member&op=editpass" method="post" onsubmit="return editpass()">
		<div class="edit-div list-div-detail">
		<table cellspacing="1" cellpadding="3" width="100%">
		  <tr>
			<th>用户名</th>
			<td><input name="user_name" class="text" type="text" value="<?php echo $this->_var['member']['user_name']; ?>" disabled="true" size="60" /></td>
		  </tr>
		  <tr>
			<th>旧密码</th>
			<td><input id="password" class="text" name="password" type="password" value="" size="60" /></td>
		  </tr>
		  <tr>
			<th>新密码</th>
			<td><input id="newpassword" class="text" name="newpassword" type="password" value="" size="60" /></td>
		  </tr>
		  <tr align="center">
			<td colspan="2">
			  <input type="hidden"  name="admin_id"       value="<?php echo $this->_var['member']['admin_id']; ?>" />
			  <input type="submit" class="button" name="Submit" value="确定" />
			</td>
		  </tr>
		</table>
		</div>
	</form>
<?php elseif ($this->_var['operation'] == 'add'): ?>
<h3>添加管理员</h3>
	<form action="index.php?action=member&op=add" method="post" onsubmit="return  checkMember(this)">
		<div class="edit-div list-div-detail">
		<table cellspacing="1" cellpadding="3" width="100%">
		  <tr>
			<th>用户名</th>
			<td><input name="user_name" class="text" type="text" value="" size="60" /></td>
		  </tr>
		  <tr>
			<th>密码</th>
			<td><input id="password" class="text" name="password" type="password" value="" size="60" /></td>
		  </tr>
		  <tr>
			<th>权限</th>
			<td>
				<?php echo $this->_var['checkbox_str']; ?>
			</td>
		  </tr>
		  <tr align="center">
			<td colspan="2">
			  <input type="submit" class="button" name="Submit"       value="确定" />
			  <input type="reset" class="button"  name="Reset"        value="重置" />
			</td>
		  </tr>
		</table>
		</div>
	</form>
<?php endif; ?>
<script type="text/javascript">
	var operation = '<?php echo $this->_var['operation']; ?>';
	<?php if ($this->_var['operation'] == 'add' || $this->_var['operation'] == 'edituser'): ?>
	window.onload = function(){
		var parents = jQuery("div.check_list");
		var valueArr;
		var allChecked = false;
		var flag,disf;
		for (var i = 0; i < parents.length; i++) {
			valueArr = jQuery(parents[i]).find("input[name='todolist[]']");
			flag = 0;
			disf = 0;
			jQuery(valueArr).each(function () { 
				if (jQuery(this).attr("checked")) { 
					flag += 1;
				}
				if (jQuery(this).attr("disabled")) { 
					disf += 1;
				}
			})
			if(flag == valueArr.length){
				jQuery(parents[i]).find("input.checkall").attr("checked",true);
			}else{
				jQuery(parents[i]).find("input.checkall").attr("checked",false);
			}
			
			if(disf == valueArr.length){
				jQuery(parents[i]).find("input.checkall").attr("disabled",true);
			}else{
				jQuery(parents[i]).find("input.checkall").attr("disabled",false);
			}
		};
		
	}
	
	function check_all (obj) {
		var parents = jQuery(obj).parents("div.check_list");
		var valueArr = jQuery(parents).find("input[name='todolist[]']");
		if(jQuery(obj).get(0).checked == true){
			for (var i = 0; i < valueArr.length; i++) {
				if(!jQuery(valueArr[i]).get(0).disabled){
					jQuery(valueArr[i]).get(0).checked = true;
				}
			}
		}else{
			for (var i = 0; i < valueArr.length; i++) {
				if(!jQuery(valueArr[i]).get(0).disabled){
					jQuery(valueArr[i]).get(0).checked = false;
				}
			}
		}
	}
	
	function check_list (obj) {
		var parents = jQuery(obj).parents("div.check_list");
		var valueArr = jQuery(parents).find("input[name='todolist[]']");
		
		//关联项操作
		if(jQuery(obj).get(0).checked){ //关联父级选中
			var related_id = jQuery(obj).attr("related");
			if(related_id){
				jQuery("#"+related_id+"_manage").get(0).checked = true;
			}
		}
		else{ //关联子级不选中
			var related_id = jQuery(obj).val();
			jQuery(valueArr).each(function () { 
				if (jQuery(this).attr("related") == related_id) { 
					jQuery(this).get(0).checked = false;
				}
			});
		}
		
		//子级若全选中，则对应选中父级，否则父级不选中
		var flag = 0;
		jQuery(valueArr).each(function () { 
			if (jQuery(this).get(0).checked) { 
				flag += 1;
			}
		});
		if(flag == valueArr.length){
			jQuery(parents).find("input.checkall").get(0).checked = true;
		}else{
			jQuery(parents).find("input.checkall").get(0).checked = false;
		}
	}
	<?php endif; ?>
</script>
<?php echo $this->smarty_insert_scripts(array('files'=>'footer.js,member_api.js')); ?>
<?php echo $this->fetch('footer.htm'); ?>