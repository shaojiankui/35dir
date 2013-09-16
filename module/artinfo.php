<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '文章详细';
$pageurl = '?mod=artinfo';
$tempfile = 'artinfo.html';
$table = $DB->table('articles');

$art_id = intval($_GET['aid']);
$cache_id = $art_id;
		
if (!$smarty->isCached($tempfile, $cache_id)) {
	$where = "a.art_status=3 AND a.art_id=$art_id";
	$art = get_one_article($where);
	if (!$art) {
		unset($art);
		redirect('./?mod=index');
	}
	
	$DB->query("UPDATE $table SET art_views=art_views+1 WHERE art_id=".$art['art_id']." LIMIT 1");
	
	$cate = get_one_category($art['cate_id']);
	$user = get_one_user($art['user_id']);
	
	$smarty->assign('site_title', $art['art_title'].' - '.$cate['cate_name'].' - '.$options['site_name']);
	$smarty->assign('site_keywords', !empty($art['art_tags']) ? $art['art_tags'] : $options['site_keywords']);
	$smarty->assign('site_description', !empty($art['art_intro']) ? $art['art_intro'] : $options['site_description']);
	$smarty->assign('site_path', get_sitepath($cate['cate_mod'], $art['cate_id']).' &raquo; '.$pagename);
	$smarty->assign('site_rss', get_rssfeed($cate['cate_mod'], $art['cate_id']));
	
	$smarty->assign('cate_id', $cate['cate_id']);
	$smarty->assign('cate_name', $cate['cate_name']);
	$smarty->assign('cate_keywords', $cate['cate_keywords']);
	$smarty->assign('cate_description', $cate['cate_description']);
	
	$art['art_content'] = str_replace('[upload_dir]', $options['site_root'].$options['upload_dir'].'/', $art['art_content']);
	$art['art_ctime'] = date('Y-m-d', $art['art_ctime']);
	
	$smarty->assign('art', $art);
	$smarty->assign('user', $user);
	$smarty->assign('prev', get_prev_article($art['art_id']));
	$smarty->assign('next', get_next_article($art['art_id']));
	$smarty->assign('related_article', get_articles($art['cate_id'], 8, false, 'ctime'));
}
		
smarty_output($tempfile, $cache_id);
?>