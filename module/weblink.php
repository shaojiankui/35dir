<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '友情链接';
$pageurl = '?mod=weblink';
$tempfile = 'weblink.html';
$table = $DB->table('weblinks');

$pagesize = 10;
$curpage = intval($_GET['page']);
if ($curpage > 1) {
	$start = ($curpage - 1) * $pagesize;
} else {
	$start = 0;
	$curpage = 1;
}
		
$deal_type = intval($_GET['type']);
$cache_id = $deal_type.'-'.$curpage;

if (!$smarty->isCached($tempfile, $cache_id)) {
	$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
	$smarty->assign('site_keywords', $options['site_keywords']);
	$smarty->assign('site_description', $options['site_description']);
	$smarty->assign('site_path', get_sitepath('weblink'));
	$smarty->assign('site_rss', get_rssfeed());
	
	$where = "l.link_hide=0";
	if ($deal_type > 0) {
		$pageurl .= '&type='.$deal_type;
		if ($deal_type > 0) $where .= " AND l.deal_type=$deal_type";
	}
			
	$results = get_weblink_list($where, 'time', 'DESC', $start, $pagesize);
	$weblinks = array();
	foreach($results as $row) {
		$user = get_one_user($row['user_id']);
		$row['user_qq'] = $user['user_qq'];
		$row['deal_type'] = $deal_types[$row['deal_type']];
		$row['link_price'] = ($row['link_price'] > 0 ? $row['link_price'] : '面议');
		$row['link_time'] = date('Y-m-d', $row['link_time']);
		$row['web_link'] = get_weblink_url($row['link_id']);
		$weblinks[] = $row;
	}
	$total = $DB->get_count($table.' l', $where);
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
			
	$smarty->assign('pagename', $pagename);
	$smarty->assign('total', $total);
	$smarty->assign('weblinks', $weblinks);
	$smarty->assign('showpage', $showpage);
	unset($weblinks);
}

smarty_output($tempfile, $cache_id);
?>