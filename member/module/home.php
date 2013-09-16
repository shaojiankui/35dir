<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '会员中心';
$pageurl = '?mod=home';
$tplfile = 'home.html';

if (!$smarty->isCached($tplfile)) {
	$smarty->assign('pagename', $pagename);
	$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
	$smarty->assign('site_path', get_sitepath());
	
	$mystat = array();
	$mystat['website'] = $DB->get_count($DB->table('websites'), array('user_id' => $myself['user_id']));
	$mystat['article'] = $DB->get_count($DB->table('articles'), array('user_id' => $myself['user_id']));
	$mystat['weblink'] = $DB->get_count($DB->table('weblinks'), array('user_id' => $myself['user_id']));
	$mystat['favorite'] = $DB->get_count($DB->table('favorites'), array('user_id' => $myself['user_id']));
	$smarty->assign('mystat', $mystat);
}

smarty_output($tplfile);
?>