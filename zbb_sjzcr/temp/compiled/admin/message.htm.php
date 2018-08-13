<?php echo $this->fetch('header.htm'); ?>
<div class="list-div message-box">
  <div style="background:#FFF; padding: 20px 50px; margin: 2px;">
    <table align="center" width="80%">
      <tr>
        <td style="font-size: 14px; font-weight: bold;"><?php echo $this->_var['msg_detail']; ?></td>
      </tr>
      <tr>
        <td id="redirectionMsg">
          <?php if ($this->_var['auto_redirect']): ?>如果您不做出选择，将在 <span id="spanSeconds">2</span> 秒后跳转到第一个链接地址。<?php endif; ?>
        </td>
      </tr>
      <tr>
        <td>
          <ul style="margin:0; padding:0 10px" class="msg-link">
            <?php $_from = $this->_var['links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'link');if (count($_from)):
    foreach ($_from AS $this->_var['link']):
?>
            <li><a href="<?php echo $this->_var['link']['href']; ?>" <?php if ($this->_var['link']['target']): ?>target="<?php echo $this->_var['link']['target']; ?>"<?php endif; ?>><?php echo $this->_var['link']['text']; ?></a></li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
          </ul>

        </td>
      </tr>
    </table>
  </div>
</div>
<?php if ($this->_var['auto_redirect']): ?>
<script language="JavaScript">
<!--
var seconds = 2;
var defaultUrl = "<?php echo $this->_var['default_url']; ?>";


onload = function()
{
  if (defaultUrl == 'javascript:history.go(-1)' && window.history.length == 0)
  {
    document.getElementById('redirectionMsg').innerHTML = '';
    return;
  }

  window.setInterval(redirection, 1000);
}
function redirection()
{
  if (seconds <= 0)
  {
	try{
		window.clearInterval();
	}catch(e){}
    
    return;
  }

  seconds --;
  document.getElementById('spanSeconds').innerHTML = seconds;

  if (seconds == 0)
  {
	try{
		window.clearInterval();
	}catch(e){}
    location.href = defaultUrl;
  }
}
//-->
</script>

<?php endif; ?>
<?php echo $this->fetch('footer.htm'); ?>