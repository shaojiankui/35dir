<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '会员激活';
$pageurl = '?mod=activate';
$tplfile = 'activate.html';
$table = $DB->table('users');

if (!$smarty->isCached($tplfile)) {
	$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
	$smarty->assign('site_keywords', $options['site_keywords']);
	$smarty->assign('site_description', $options['site_description']);
	$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);
	
	$user_id = intval($_GET['uid']);
	$verify_code = trim($_GET['code']);
	
	$user = $DB->fetch_one("SELECT user_id, verify_code, join_time FROM $table WHERE user_id=$user_id LIMIT 1");
	if (!$user) {
		msgbox('您还不是本站的会员！', '?mod=register');
	}
	
	$twodays = $user['join_time'] + (2 * 24 * 3600);
	if ($twodays >= time()) {
		if ($verify_code == $user['verify_code']) {
			$DB->update($table, array('user_status' => 1), array('user_id' => $user['user_id']));
			$message = '帐号激活成功！<br /><br /><a href="?mod=login">立即登录账户>></a>';
		} else {
			$message = '帐号激活失败！';
		}
	} else {
		$message = '可能是因为超过48小时没有完成验证，该链接地址已经失效！';
	}
	
	$smarty->assign('message', $message);
}

smarty_output($tplfile);
?>