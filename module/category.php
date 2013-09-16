<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '分类浏览';
$pageurl = '?mod=category';
$tempfile = 'category.html';
$table = $DB->table('categories');

if (!$smarty->isCached($tempfile)) {
	$categories = get_categories();
	
	$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
	$smarty->assign('site_keywords', '开放分类，网址分类，目录分类，行业分类');
	$smarty->assign('site_description', '对网站进行很详细的分类，这样有助于帮你找到感兴趣的内容。');
	$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);
	$smarty->assign('site_rss', get_rssfeed());
	
	$smarty->assign('pagename', $pagename);
	$smarty->assign('total', count($categories));
	$smarty->assign('categories', $categories);
	unset($categories);
}

smarty_output($tempfile);
?>