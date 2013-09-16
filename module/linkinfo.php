<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '链接详细';
$pageurl = '?mod=linkinfo';
$tempfile = 'linkinfo.html';
$table = $DB->table('weblinks');

$link_id = intval($_GET['lid']);
$cache_id = $link_id;
		
if (!$smarty->isCached($tempfile, $cache_id)) {
	$where = "w.web_status=3 AND l.link_id=$link_id";
	$link = get_one_weblink($where);
	if (!$link) {
		unset($link);
		redirect('./?mod=index');
	}
	
	$DB->query("UPDATE $table SET link_views=link_views+1 WHERE link_id=".$link['link_id']." LIMIT 1");
	
	$cate = get_one_category($link['cate_id']);
	$user = get_one_user($link['user_id']);
	
	$smarty->assign('site_title', $link['link_name'].' - '.$options['site_name']);
	$smarty->assign('site_keywords', '友情链接交换，友情链接交易，友情链接出售');
	$smarty->assign('site_description', !empty($link['link_intro']) ? $link['link_intro'] : $options['site_description']);
	$smarty->assign('site_path', get_sitepath('weblink').' &raquo; '.$cate['cate_name'].' &raquo; '.$pagename);
	$smarty->assign('site_rss', get_rssfeed($link['cate_id']));
	
	$smarty->assign('cate_id', $cate['cate_id']);
	$smarty->assign('cate_name', $cate['cate_name']);
	$smarty->assign('cate_keywords', $cate['cate_keywords']);
	$smarty->assign('cate_description', $cate['cate_description']);
	
	$link['web_furl'] = format_url($link['web_url']);
	$link['web_pic'] = get_webthumb($link['web_pic']);
	$link['deal_type'] = $deal_types[$link['deal_type']];
	$link['link_type'] = $link_types[$link['link_type']];
	$link['link_pos'] = $link_pos[$link['link_pos']];
	$link['link_price'] = $link['link_price'] > 0 ? $link['link_price'].'元 / 月' : '商谈';
	$link['link_time'] = date('Y-m-d', $link['link_time']);
	
    $smarty->assign('link', $link);
	$smarty->assign('user', $user);
}
		
smarty_output($tempfile, $cache_id);
?>