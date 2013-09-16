<?php
require('common.php');

$pagetitle = '修改密码';
$fileurl = 'editpwd.php';
$tempfile = 'editpwd.html';
$table = $DB->table('users');

if ($action == 'saveedit') {
	$user_id = intval($_POST['user_id']);
	$user_email = trim($_POST['user_email']);
	$user_pass = trim($_POST['user_pass']);
	$new_pass = trim($_POST['new_pass']);
	$new_pass1 = trim($_POST['new_pass1']);
	
	if (empty($user_email) || !is_valid_email($user_email)) {
		msgbox('请输入正确的电子邮箱！');
	}
	
	if (empty($user_pass)) {
		msgbox('请输入原始密码！');
	}
	
	if (empty($new_pass)) {
		msgbox('请输入新密码！');
	}
	
	if (empty($new_pass1)) {
		msgbox('请输入确认密码！');
	}
	
	if ($new_pass != $new_pass1) {
		msgbox('您两次输入的密码不一致！');
	}
	
	$user_pass = md5($user_pass);
	$new_pass = md5($new_pass);
	
	$user = $DB->fetch_one("SELECT user_id, user_pass FROM $table WHERE user_id='$user_id'");
	if (!$user) {
		msgbox('不存在此用户！');
	} else {
		if ($user_pass != $user['user_pass']) {
			msgbox('您输入的原始密码不正确！');
		}
		$DB->update($table, array('user_email' => $user_email, 'user_pass' => $new_pass), array('user_id' => $user['user_id']));
	}
	
	msgbox('帐号密码修改成功！', $fileurl);
}

smarty_output($tempfile);
?>