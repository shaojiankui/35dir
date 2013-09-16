<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '修改密码';
$pageurl = '?mod=editpwd';
$tplfile = 'editpwd.html';
$table = $DB->table('users');

if (!$smarty->isCached($tplfile)) {
	$smarty->assign('pagename', $pagename);
	$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
	$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);
	
	if ($_POST['do'] == 'save') {
		$old_pass = trim($_POST['old_pass']);
		$new_pass = trim($_POST['new_pass']);
		$new_pass1 = trim($_POST['new_pass1']);
		
		if (empty($old_pass)) {
			msgbox('请输入原始密码！');
		} else {
			$user = $DB->fetch_one("SELECT user_pass FROM $table WHERE user_id=".$myself['user_id']);
			if ($user['user_pass'] != md5($old_pass)) {
				unset($user);
				msgbox('您输入的原始密码不正确！');
			}
		}
		
		if (empty($new_pass)) {
			msgbox('请输入新密码！');
		}
		
		if (empty($new_pass1)) {
			msgbox('请输入确认密码！');
		}
		
		if ($new_pass != $new_pass1) {
			msgbox('两次密码输入不一致，请重新输入！');
		}
		
		$data = array('user_pass' => md5($new_pass));
		$where = array('user_id' => $myself['user_id'],);
		
		$DB->update($table, $data, $where);
		msgbox('账户密码修改成功！', $pageurl);
	}
}

smarty_output($tplfile);
?>