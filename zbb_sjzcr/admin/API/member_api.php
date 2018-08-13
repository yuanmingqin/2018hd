<?php
require_once '../../source/class/class_core.php';
$kldapp = C::app();
$kldapp->reject_robot();
$kldapp->init();
require_once '../includes/common.php';
$action = $_REQUEST['ac'] ? $_REQUEST['ac'] : '';
$return = array('errcode'=>'0','errmsg'=>'');
if($action == 'edituser'){
	$admin_id=intval($_POST['admin_id']);
	if($admin_id == 1 && $_COOKIE['cutv_uid'] != 1) {
		$return['errcode'] = -1;
		$return['errmsg'] = "您没有权限执行该操作";
	}else{
		$newpassword=$_POST['newpassword'];
		$password=$_POST['password'];
		$todolist=$_POST['todolist'];
		if($newpassword){
			if(strlen($newpassword) < 6){
				die_return("-2","密码长度不能少于6位");
			}else{
				$user = DB::fetch_first("SELECT password,dpass FROM ".DB::table('admin_user')." WHERE admin_id='$admin_id'");
				if(empty($user)){
					die_return("-3","用户不存在");
				}
				else if($user['password'] != (empty($user['dpass']) ? md5($password) : md5(md5($password).$user['dpass']))){
					die_return("-4","旧密码输入错误");
				}
			}
		}
				
		//非admin管理员编辑权限,只能继承
		if($_COOKIE['cutv_uid'] != 1){ //非admin时过滤权限
			for($k=0; $k<count($todolist); $k++){
				if(!in_array($todolist[$k], $login_todo)){
					array_splice($todolist,$k,1);
					$k--;
				}
			}
			
			$member = C::t("admin_user")->fetch_by_id($admin_id);
			if($member){
				$old_todo = unserialize($member['todolist']);
				for($j=0; $j<count($old_todo); $j++){
					if(in_array($old_todo[$j], $login_todo)){
						array_splice($old_todo,$j,1);
						$j--;
					}
				}
				
				$todolist = array_merge($todolist, $old_todo);
				$todolist = array_unique($todolist);
				
				//去除权限管理项
				foreach ($todolist as $key => $value) {
					if($value == 'member'){
						array_splice($todolist,$key,1);
						break;
					}
				}
			}
			else{
				die_return("-5","参数错误");
			}
		}
		/* 更新数据库中对应id管理员 */
		C::t("admin_user")->update_by_id($admin_id,$newpassword,serialize($todolist));
		//写入日志
		admin_log("编辑管理员,ID:".$admin_id);
	}
	
}elseif($action == 'editpass'){
	$admin_id= intval($_POST['admin_id']);
	$password=$_POST['password'];
	$newpassword=$_POST['newpassword'];	
	if(empty($newpassword)){
		die_return("-1","密码不能为空");
	}
	else if(strlen($newpassword) < 6){
		die_return("-2","密码长度不能少于6位");
	}
	$user = DB::fetch_first("SELECT password,dpass FROM ".DB::table('admin_user')." WHERE admin_id='$admin_id'");
	if(empty($user)){
		die_return("-3","用户不存在");
	}else if($user['password'] != (empty($user['dpass']) ? md5($password) : md5(md5($password).$user['dpass']))){
		die_return("-4","旧密码输入错误");
	}	
	/* 更新数据库中对应id管理员 */
	C::t("admin_user")->editpass_by_id($admin_id,$newpassword);
	admin_log("编辑管理员,ID:".$admin_id);
}else if($action == 'add'){
		$user_name=$_POST['user_name'];
		$password=$_POST['password'];
		$todolist=$_POST['todolist'];
		$add_time = time();
		if(empty($user_name) || empty($password) || empty($todolist)){
			die_return("-1","输入值不能为空");
		}elseif(DB::result_first("SELECT user_name FROM ".DB::table('admin_user')." WHERE user_name='$user_name'")){
			die_return("-2","用户名已经存在");
		}
		else if(strlen($password) < 6){
			die_return("-3","密码长度不能少于6位");
		}
		$todolist = array_unique($todolist);
		//非admin不允许编辑权限管理的项
		if($_COOKIE['cutv_uid'] != 1) {
			//去除权限管理项
			foreach ($todolist as $key => $value) {
				if($value == 'member'){
					array_splice($todolist,$key,1);
					break;
				}
			}
		}		
		/* 添加管理员 */
		if(C::t("admin_user")->add_member($user_name,$password,serialize($todolist),$add_time)){
			admin_log("添加管理员,name:".$user_name);	
		}else{
			die_return("-4","添加失败，请稍后重试！");
		}	
}elseif($action == 'del') {
	$admin_id = intval($_GET['id']);
	if(!$admin_id){
		die_return("-1","参数错误");
	}
	if($admin_id == 1) {
		die_return("-2","您没有权限执行该操作");
	}
	//非admin不允许删除带权限管理的用户
	if($_COOKIE['cutv_uid'] != 1) {
		$member = C::t("admin_user")->fetch_by_id($admin_id);
		if($member){
			if(in_array('member', unserialize($member['todolist']))){
				die_return("-3","您没有权限执行该操作");
			}
		}
		else{
			sys_msg('参数错误', 0, $link);
			die_return("-4","参数错误");
		}
	}
	
	/* 删除数据库中对应id管理员 */
	C::t("admin_user")->del_by_id($admin_id);

	//写入日志
	admin_log("删除管理员,ID:".$admin_id);
}else{
	$return['errcode'] = -404;
	$return['errmsg'] = 'error action';
}
echo json_encode($return);
exit();