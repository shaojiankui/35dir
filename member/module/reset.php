<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '密码重置';
$pageurl = '?mod=reset';
$tplfile = 'reset.html';
$table = $DB->table('users');

if (!$smarty->isCached($tplfile)) {
	$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
	$smarty->assign('site_keywords', $options['site_keywords']);
	$smarty->assign('site_description', $options['site_description']);
	$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);
	
	$user_id = intval($_GET['uid']);
	$verify_code = trim($_GET['code']);
	if (empty($verify_code)) {
		msgbox('校验代码错误！');
	}
	$smarty->assign('code', $verify_code);
	
	$user = $DB->fetch_one("SELECT user_id FROM $table WHERE user_id='$user_id'");
    if (!$user) {
       	msgbox('您还不是本站会员！', '?mod=register');
	}
	
	if ($_POST['action'] == 'save') {
		$user_email = trim($_POST['email']);
		$verify_code = trim($_POST['code']);
		$user_pass = trim($_POST['pass']);
		$user_pass1 = trim($_POST['pass1']);
		
		if (empty($user_email) || !is_valid_email($user_email)) {
			msgbox('请输入有效的电子邮箱！');
		}
		
		if (empty($verify_code)) {
			msgbox('请输入校验码！');
		}
		
		if (empty($user_pass)) {
			msgbox('请输入新密码！');
		} else {
			if (strlen($user_pass) < 6 || strlen($user_pass) > 20) {
				msgbox('密码长度请保持在6-20个字符！');
			}
		}
				
		if (empty($user_pass1)) {
			msgbox('请输入确认密码！');
		}
		
		if ($user_pass != $user_pass1) {
			msgbox('两次密码输入不一致，请重新输入！');
		}
		
		$user = $DB->fetch_one("SELECT user_id, verify_code FROM $table WHERE user_email='$user_email'");
    	if (!$user) {
        	msgbox('您还不是本站会员！', '?mod=register');
    	} else {
			if ($verify_code != $user['verify_code']) {
				msgbox('校验代码错误或已失效！');
			}
			$DB->update($table, array('user_pass' => md5($user_pass)), array('user_id' => $user['user_id']));
			
			msgbox('恭喜！您的密码已重置成功！', '?mod=login');	
		}
	}
}

smarty_output($tplfile);
?>