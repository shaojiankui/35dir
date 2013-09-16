<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

require(APP_PATH.'module/category.php');
require(APP_PATH.'module/favorite.php');

$pageurl = '?mod=favorite';
$tplfile = 'favorite.html';
$table = $DB->table('favorites');

$action = isset($_GET['act']) ? $_GET['act'] : 'list';
$smarty->assign('action', $action); 

if (!$smarty->isCached($tplfile)) {
	/** list */
	if ($action == 'list') {
		$pagename = '我的收藏';
		$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
		$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);
		
		$pagesize = 10;
		$curpage = intval($_GET['page']);
		if ($curpage > 1) {
			$start = ($curpage - 1) * $pagesize;
		} else {
			$start = 0;
			$curpage = 1;
		}
		
		$where = "f.user_id=".$myself['user_id'];
		$favorites = get_favorite_list($where, 'ftime', 'DESC', $start, $pagesize);
		$total = $DB->get_count($table.' f', $where);
		$showpage = showpage($pageurl, $total, $curpage, $pagesize);
		
		$smarty->assign('pagename', $pagename);
		$smarty->assign('favorites', $favorites);
		$smarty->assign('total', $total);
		$smarty->assign('showpage', $showpage);
	}
	
	/** del */
	if ($action == 'del') {
		$fav_ids = (array) ($_POST['fid'] ? $_POST['fid'] : $_GET['fid']);
	
		$DB->delete($table, 'fav_id IN ('.dimplode($fav_ids).')');
		unset($fav_ids);
	
		redirect($pageurl);
	}
}

smarty_output($tplfile);
?>