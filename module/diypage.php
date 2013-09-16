<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '';
$pageurl = '?mod=diypage';
$tempfile = 'diypage.html';
$table = $DB->table('pages');

$page_id = intval($_GET['pid']);
$cache_id = $page_id;
		
if (!$smarty->isCached($tempfile, $cache_id)) {
	$page = get_one_page($page_id);
	if (!$page) {
		unset($page);
		redirect('?mod=index');
	}
	
	$smarty->assign('site_title', $page['page_name'].' - '.$options['site_title']);
	$smarty->assign('site_keywords', $options['site_keywords']);
	$smarty->assign('site_description', $options['site_description']);
	$smarty->assign('site_path', get_sitepath().' &raquo; '.$page['page_name']);
	$smarty->assign('site_rss', get_rssfeed());
    $smarty->assign('page_id', $page_id);
	$smarty->assign('page', $page);
}
		
smarty_output($tempfile, $cache_id);
?>