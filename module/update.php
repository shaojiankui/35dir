<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '最近更新';
$pageurl = '?mod=update';
$tempfile = 'update.html';
$table = $DB->table('websites');

$pagesize = 10;
$curpage = intval($_GET['page']);
if ($curpage > 1) {
	$start = ($curpage - 1) * $pagesize;
} else {
	$start = 0;
	$curpage = 1;
}
		
$setdays = intval($_GET['days']);
$cache_id = $setdays.'-'.$curpage;

if (!$smarty->isCached($tempfile, $cache_id)) {
	$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
	$smarty->assign('site_keywords', '最近更新，最新收录，每日最新');
	$smarty->assign('site_description', '让你及时了解最新收录内容，可按时间段（最近24小时、三天内、一星期、一个月、一年、所有时间）查询，让你及时了解网站在某一时间段内的收录情况。');
	$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);
	$smarty->assign('site_rss', get_rssfeed());
	
	$newarr = array();
	$i = 0;
	foreach ($timescope as $key => $val) {
		$newarr[$i]['time_id'] = $key;
		$newarr[$i]['time_text'] = $val;
		$newarr[$i]['time_link'] = $pageurl.'&days='.$key;
		$i++;
	}
	
	$where = "w.web_status=3";
	if ($setdays > 0) {
		$smarty->assign('site_title', $timescope[$setdays].'收录详情 - '.$pagename.' - '.$options['site_name']);
		$smarty->assign('site_path', get_sitepath().' &raquo; <a href="'.$pageurl.'">'.$pagename.'</a> &raquo; '.$timescope[$setdays]);
		$pageurl .= '&days='.$setdays;
		
		$now = time();
		switch ($setdays) {
			case 1 :
				$time = $now - (3600 * 24);
				break;
			case 3 :
				$time = $now - (3600 * 24 * 3);
				break;
			case 7 :
				$time = $now - (3600 * 24 * 7);
				break;
			case 30 :
				$time = $now - (3600 * 24 * 30);
				break;
			case 365 :
				$time = $now - (3600 * 24 * 365);
				break;
			default :
				$time = 0;
				break;
		}
		$where .= " AND w.web_ctime>=$time";
	}
			
	$websites = get_website_list($where, 'web_ctime', 'DESC', $start, $pagesize);
	$total = $DB->get_count($table.' w', $where);
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
			
	$smarty->assign('pagename', $pagename);
	$smarty->assign('timescope', $newarr);
	$smarty->assign('timestr', $timescope[$setdays]);
	$smarty->assign('days', $setdays);
	$smarty->assign('total', $total);
	$smarty->assign('websites', $websites);
	$smarty->assign('showpage', $showpage);
	unset($websites);
}
	
smarty_output($tempfile, $cache_id);
?>