<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '搜索结果';
$pageurl = '?mod=search';
$tempfile = 'search.html';
$table = $DB->table('websites');

//搜索页不缓存
$smarty->caching = false;

$pagesize = 10;
$curpage = intval($_GET['page']);
if ($curpage > 1) {
	$start = ($curpage - 1) * $pagesize;
} else {
	$start = 0;
	$curpage = 1;
}
		
$strtype = strtolower(trim($_GET['type']));
$keyword = addslashes(trim($_GET['query']));

if (!$smarty->isCached($tempfile)) {
	$where = "w.web_status=3";
	if ($keyword) {
		$pageurl .= '&type='.$strtype.'&query='.urlencode($keyword);
				
		$smarty->assign('site_title', $keyword.' - '.$pagename.' - '.$options['site_name']);
		$smarty->assign('site_keywords', $keyword.'，搜索结果，查询结果');
		$smarty->assign('site_description', '以下是与关键字(词)“'.$keyword.'”相关的结果。');
		$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename.' &raquo; <font color="#ff0000">'.$keyword.'</font>');
		$smarty->assign('rss_feed', get_rssfeed());
				
		switch ($strtype) {
			case 'name' :
				$where = "w.web_name like '%$keyword%'";
				break;
			case 'url' :
				$where = "w.web_url like '%$keyword%'";
				break;
			case 'tags' :
				$where = "w.web_tags like '%$keyword%'";
				break;
			case 'intro' :
				$where = "w.web_intro like '%$keyword%'";
			default :
				$where = "w.web_name like '%$keyword%'";
				break;
			}
		}
			
		$websites = get_website_list($where, 'web_ctime', 'DESC', $start, $pagesize);
		$total = $DB->get_count($table.' w', $where);
		$showpage = showpage($pageurl, $total, $curpage, $pagesize);
			
		$smarty->assign('pagename', $pagename);
		$smarty->assign('category_list', get_categories());
		$smarty->assign('archives', get_archives());
		$smarty->assign('keyword', $keyword);
		$smarty->assign('total', $total);
		$smarty->assign('websites', $websites);
		$smarty->assign('showpage', $showpage);
		unset($websites);
}
	
smarty_output($tempfile, $cache_id);
?>