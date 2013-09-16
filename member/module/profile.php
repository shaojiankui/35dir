<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '个人资料';
$pageurl = '?mod=profile';
$tplfile = 'profile.html';
$table = $DB->table('users');

if (!$smarty->isCached($tplfile)) {
	$smarty->assign('pagename', $pagename);
	$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
	$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);
	
	if ($_POST['do'] == 'save') {
		$nick_name = trim($_POST['nick_name']);
		$user_qq = trim($_POST['user_qq']);
		
		$data = array(
			'nick_name' => $nick_name,
			'user_qq' => $user_qq,
		);
		
		$where = array(
			'user_id' => $myself['user_id'],
		);
		
		$DB->update($table, $data, $where);
		msgbox('个人资料修改成功！', $pageurl);
	}
}

smarty_output($tplfile);
?>