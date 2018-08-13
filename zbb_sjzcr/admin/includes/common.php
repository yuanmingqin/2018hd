<?php 
/**
 *    秘密信息
 *    
 *    后台管理通用方法
 */
if(!defined('IN_CUTVSYS')) {
	exit('Access Denied');
}

/**
 * 系统提示信息
 *
 * @access      public
 * @param       string      msg_detail      消息内容
 * @param       int         msg_type        消息类型， 0消息，1错误，2询问
 * @param       array       links           可选的链接
 * @param       boolen      $auto_redirect  是否需要自动跳转
 * @return      void
 */
function sys_msg($msg_detail, $msg_type = 0, $links = array(), $auto_redirect = true)
{
	global $config;
    if (count($links) == 0)
    {
        $links[0]['text'] = '返回上一页';
        $links[0]['href'] = 'javascript:history.go(-1)';
    }

	$GLOBALS['smarty']->template_dir = CUTV_ROOT.'./admin/templates';
	$GLOBALS['smarty']->cache_dir = CUTV_ROOT.'./temp/caches/admin';
	$GLOBALS['smarty']->compile_dir = CUTV_ROOT.'./temp/compiled/admin';
    $GLOBALS['smarty']->assign('ur_here',     '系统信息');
    $GLOBALS['smarty']->assign('msg_detail',  $msg_detail);
    $GLOBALS['smarty']->assign('msg_type',    $msg_type);
    $GLOBALS['smarty']->assign('links',       $links);
    $GLOBALS['smarty']->assign('default_url', $links[0]['href']);
    $GLOBALS['smarty']->assign('auto_redirect', $auto_redirect);

	if($_COOKIE['cutv_username']){
		$menu = C::t("sessions_data")->fetch_by_sesskey($_COOKIE['cutv_sid']);
		$GLOBALS['smarty']->assign('menu_json', $menu['data']);
		$GLOBALS['smarty']->assign('username', $_COOKIE['cutv_username']);
	}
	
    $GLOBALS['smarty']->display('message.htm');

    exit;
}

/**
 * 判断管理员对某一个操作是否有权限。
 *
 * 根据当前对应的action_code，然后再和用户session里面的action_list做匹配，以此来决定是否可以继续执行。
 * @param     string    $priv_str    操作对应的priv_str
 * @param     string    $msg_type       返回的类型
 * @return true/false
 */
function admin_priv($priv_str, $msg_type = '' , $msg_output = true)
{
	if($_COOKIE['cutv_sid']) {
		$member = C::t("admin_sessions")->fetch_by_key($_COOKIE['cutv_sid']);
		if($member){
			if($member['adminid'] == 1) {
				return true;
			}
			
			$priv = unserialize($member['data']);
			if(!in_array($priv_str, $priv)) {
				sys_msg('您没有权限执行该操作');
			}
			return $priv;
		}
		else{
			header('Location:login.php?action=logout');
		}
	}
	else{
		header('Location:login.php?action=logout');
	}
}

function multi($num, $perpage, $curpage, $mpurl, $maxpages = 0, $page = 10, $autogoto = FALSE, $simple = FALSE, $jsfunc = FALSE) {
	global $_G;
	$ajaxtarget = !empty($_GET['ajaxtarget']) ? " ajaxtarget=\"".dhtmlspecialchars($_GET['ajaxtarget'])."\" " : '';

	$a_name = '';
	if(strpos($mpurl, '#') !== FALSE) {
		$a_strs = explode('#', $mpurl);
		$mpurl = $a_strs[0];
		$a_name = '#'.$a_strs[1];
	}
	if($jsfunc !== FALSE) {
		$mpurl = 'javascript:'.$mpurl;
		$a_name = $jsfunc;
		$pagevar = '';
	} else {
		$pagevar = 'page=';
	}

	$shownum = $showkbd = FALSE;
	$showpagejump = TRUE;
	$lang['prev'] = '<<';
	$lang['next'] = '>>';
	$lang['pageunit'] = '页';
	$lang['total'] = '共';
	$lang['pagejumptip'] = '输入页码，按回车快速跳转';
	
	$dot = '...';
	$multipage = '';
	if($jsfunc === FALSE) {
		$mpurl .= strpos($mpurl, '?') !== FALSE ? '&amp;' : '?';
	}

	$realpages = 1;
	$_G['page_next'] = 0;
	$page -= strlen($curpage) - 1;
	if($page <= 0) {
		$page = 1;
	}
	if($num > $perpage) {

		$offset = floor($page * 0.5);

		$realpages = @ceil($num / $perpage);
		$curpage = $curpage > $realpages ? $realpages : $curpage;
		$pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;

		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}
		$_G['page_next'] = $to;
		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.$pagevar.'1'.$a_name.'" class="first"'.$ajaxtarget.'>1 '.$dot.'</a>' : '').
		($curpage > 1 && !$simple ? '<a href="'.$mpurl.$pagevar.($curpage - 1).$a_name.'" class="prev"'.$ajaxtarget.'>'.$lang['prev'].'</a>' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<strong>'.$i.'</strong>' :
			'<a href="'.$mpurl.$pagevar.$i.($ajaxtarget && $i == $pages && $autogoto ? '#' : $a_name).'"'.$ajaxtarget.'>'.$i.'</a>';
		}
		$multipage .= ($to < $pages ? '<a href="'.$mpurl.$pagevar.$pages.$a_name.'" class="last"'.$ajaxtarget.'>'.$dot.' '.$realpages.'</a>' : '');
		$multipage .= ($curpage < $pages && !$simple ? '<a href="'.$mpurl.$pagevar.($curpage + 1).$a_name.'" class="nxt"'.$ajaxtarget.'>'.$lang['next'].'</a>' : '').
		($showkbd && !$simple && $pages > $page && !$ajaxtarget ? '<kbd><input type="text" name="custompage" size="3" onkeydown="if(event.keyCode==13) {window.location=\''.$mpurl.$pagevar.'\'+this.value; doane(event);}" /></kbd>' : '');
		$multipage .= '<label><input type="text" name="custompage" class="px" size="2" title="'.$lang['pagejumptip'].'" value="'.$curpage.'" onkeydown="if(event.keyCode==13) {window.location=\''.$mpurl.$pagevar.'\'+this.value; doane(event);}" /><span title="'.$lang['total'].' '.$pages.' '.$lang['pageunit'].'"> / '.$pages.' '.$lang['pageunit'].'</span></label>';
		$multipage = $multipage ? '<div class="pg">'.($shownum && !$simple ? '<em>&nbsp;'.$num.'&nbsp;</em>' : '').$multipage.'&nbsp;&nbsp;&nbsp;&nbsp;每页'.$perpage.'条&nbsp;&nbsp;共'.$num.'条</div>' : '';
	}
	$maxpage = $realpages;
	return $multipage;
}

/**
 * 记录管理员的操作内容
 *
 * @access  public
 * @param   string      $content    操作的内容
 * @return  void
 */
function admin_log($content, $kuid)
{
    $sql = 'INSERT INTO '  . DB::table('admin_log') . ' (log_time, adminid, log_info, ip_address) ' .
            " VALUES ('" . time() . "', ".($kuid ? $kuid : $_COOKIE['cutv_uid']).", '" . $content . "', '" . getglobal('clientip') . "')";
    DB::query($sql);
}

function checkadminlogin() {
	if($_COOKIE['cutv_sid']) {
		$user = C::t("admin_sessions")->fetch_by_key($_COOKIE['cutv_sid']);
		if($user['adminid']==$_COOKIE['cutv_uid'] && $user['expiry']>=time()) {
			$data = array(
				'expiry' => time()+15*60,
			);
			DB::update('admin_sessions', $data, "sesskey='$_COOKIE[cutv_sid]'");
			mySetCookie('cutv_tid', $data['expiry']);
		} else {
			header('Location:login.php?action=logout');
		}
	} else {
		header('Location:login.php');
	}
}

/**
 * 隐藏没有权限的菜单项
 *
 * @access  public
 * @param   array      $perm_list  菜单数组 
 * @return  array 	$permlist 有权限的菜单项
 */
function check_priv(&$perm_list, $todolist){
	static $permlist = array();
	
	for($k=0; $k<count($perm_list); $k++){
		$value = &$perm_list[$k];
		if(is_array($value)){
			if(array_key_exists('child',$value)){
				check_priv($value['child'],$todolist);
			}else{
				$flag = in_array($value['action'], $todolist) ? 1 : 0;
				$permlist[] = array('id' => $value['action'], 'name' => $value['title']);
				
				if(!$flag){
					array_splice($perm_list,$k,1);
					$k--;
				}
			}
		}	
	}			
	return $permlist;
}

/**
 * 隐藏没有子菜单的菜单项
 *
 * @access  public
 * @param   array      $arr  菜单数组 
 * @return  void 	
 */
function array_empty_filter(&$arr)
{
	for($k=0; $k<count($arr); $k++){
		$value = &$arr[$k];
		if(empty($value['child'])){
			array_splice($arr,$k,1);
			$k--;
		}
	}
}

//模拟socket请求
function curlpost($ip, $port, $host, $url) {
	$errstr = '';
	$errno = '';
	$fp = fsockopen ($ip, $port, $errno, $errstr, 2);
	if (!$fp) {
		return false;
	} else {
		$out  = "GET $url HTTP/1.1\r\n";
		$out .= "Host:$host\r\n";
		$out .= "Connection: close\r\n\r\n";
		fputs ($fp, $out);
		fclose ($fp);
		return true;
	}
}

//清除缓存
function clear_cache(){
	global $_G;
	
	clear_all_files();
	
	$clearResult = array();
	$iplist = getUrlData(str_replace("/admin","",$_G['siteurl']).'/ip.php');
    if ($iplist) {
		$iparr = json_decode($iplist);
		if(is_array($iparr)){
			foreach($iparr as $ip) {
				$ips = explode(':', $ip);
				$saddr = explode(".",$ips[0]);
				
				if(curlpost($ips[0], $ips[1], $_SERVER['HTTP_HOST'], "/kldwx/updatecache.php")){
					$clearResult[] = $saddr[count($saddr)-1]." -> 成功";
				}
				else{
					$clearResult[] = $saddr[count($saddr)-1]." -> 失败";
				}
			}
		}
	}
	
	return $clearResult;
}
function get_tree_menu($menulist,$cid='id',$fid='parent_id'){
	$rows = array();
	foreach ($menulist as $key => $value) {
		$rows[$value[$cid]] = $value;
	}
	
	$t = array();
	foreach ($rows as $id => $item) {
		if ($item[$fid]) {
			$rows[$item[$fid]][$item[$cid]] = &$rows[$item[$cid]];
			$t[] = $id;
		}
	}
	
	foreach($t as $u) {
		unset($rows[$u]);
	}
	return $rows;
}
function check_manage_todo(&$perm_list, $member='', $login_todo){
	//非admin只能编辑自己具备的权限
	$is_allow = true;
	if($_COOKIE['cutv_uid'] != 1) {
		$is_allow = false;
	}
	
	$checkbox_str = '';
	$idx = 0;	
	foreach ($perm_list as $key => &$value) {
		if(is_array($value)){
			if(array_key_exists('child',$value)){
				$checkbox_str .= '<div class="check_list"><label class="all" for="allmanage_'.(++$idx).'"><input type="checkbox" onclick="check_all(this);" class="checkall" id="allmanage_'.$idx.'" value="">'.$value['title'].'</label>';
			}
		}
		if(is_array($value)){
			if(array_key_exists('child',$value)){
				$checkbox_str .= check_manage_todo($value['child'], $member, $login_todo);
			}else{
				$flag = 0;
				if($member){
					$flag = in_array($value['action'], $member['todolist']) ? 1 : 0;
				}
				
				if($flag){
					$checked = ' checked="true" ';
				}else{
					$checked = '';
				}
				if(!$is_allow && ($value['action'] == 'member' || !in_array($value['action'], $login_todo))){
					$checked .= ' disabled="true" ';
				}
				$checkbox_str .= '<div class="subcheck"><label for="'.$value['action'].'_manage"><input onclick="check_list(this);" type="checkbox" related="'.($value['related'] ? $value['related'] : '').'" id="'.$value['action'].'_manage"'.$checked.'value="'.$value['action'].'" name="todolist[]">'.$value['title'].'</label></div>';
			}
		}
		if(is_array($value)){
			if(array_key_exists('child',$value)){
				$checkbox_str .= '</div>';
			} 
		}	
	}			
	return $checkbox_str;
}
function die_return($errcode,$errmsg){
	$return = array();
	$return['errcode'] = $errcode;
	$return['errmsg'] = $errmsg;
	echo json_encode($return);
	exit();
}
?>