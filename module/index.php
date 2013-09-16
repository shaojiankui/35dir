<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '网站首页';
$pageurl = '?mod=index';
$tempfile = 'index.html';

if (!$smarty->isCached($tempfile)) {
	$smarty->assign('site_title', $options['site_title']);
	$smarty->assign('site_keywords', $options['site_keywords']);
	$smarty->assign('site_description', $options['site_description']);
	$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);
	$smarty->assign('site_rss', get_rssfeed());
}

smarty_output($tempfile);
?>