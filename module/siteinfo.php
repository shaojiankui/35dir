<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '站点详细';
$pageurl = '?mod=siteinfo';
$tempfile = 'siteinfo.html';
$table = $DB->table('webdata');

$web_id = intval($_GET['wid']);
$cache_id = $web_id;
		
if (!$smarty->isCached($tempfile, $cache_id)) {
	$where = "w.web_status=3 AND w.web_id=$web_id";
	$web = get_one_website($where);
	if (!$web) {
		unset($web);
		redirect('./?mod=index');
	}
	
	$DB->query("UPDATE $table SET web_views=web_views+1 WHERE web_id=".$web['web_id']." LIMIT 1");
	
	$cate = get_one_category($web['cate_id']);
	$user = get_one_user($web['user_id']);
	
	$smarty->assign('site_title', $web['web_name'].' - '.$cate['cate_name'].' - '.$options['site_name']);
	$smarty->assign('site_keywords', !empty($web['web_tags']) ? $web['web_tags'] : $options['site_keywords']);
	$smarty->assign('site_description', !empty($web['web_intro']) ? $web['web_intro'] : $options['site_description']);
	$smarty->assign('site_path', get_sitepath($cate['cate_mod'], $web['cate_id']).' &raquo; '.$pagename);
	$smarty->assign('site_rss', get_rssfeed($cate['cate_mod'], $web['cate_id']));
	
	$smarty->assign('cate_id', $cate['cate_id']);
	$smarty->assign('cate_name', $cate['cate_name']);
	$smarty->assign('cate_keywords', $cate['cate_keywords']);
	$smarty->assign('cate_description', $cate['cate_description']);
	
	$web['web_furl'] = format_url($web['web_url']);
	$web['web_pic'] = get_webthumb($web['web_pic']);
	$web['web_ip'] = long2ip($web['web_ip']);
	$web['web_ctime'] = date('Y-m-d', $web['web_ctime']);
	$web['web_utime'] = date('Y-m-d', $web['web_utime']);
	
	/** tags */
	$web_tags = get_format_tags($web['web_tags']);
	$smarty->assign('web_tags', $web_tags);
	
    $smarty->assign('web', $web);
	$smarty->assign('user', $user);
	$smarty->assign('related_website', get_websites($web['cate_id'], 10, false, false, 'ctime'));
}
		
smarty_output($tempfile, $cache_id);
?>