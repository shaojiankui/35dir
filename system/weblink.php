<?php
require('common.php');
require(APP_PATH.'module/weblink.php');
require(APP_PATH.'module/user.php');
require(APP_PATH.'module/prelink.php');

$fileurl = 'weblink.php';
$tempfile = 'weblink.html';
$table = $DB->table('weblinks');

if (!isset($action)) $action = 'list';

/** list */
if ($action == 'list') {
	$pagetitle = '友链列表';
	
	$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));
	$keyurl = !empty($keywords) ? '?keywords='.urlencode($keywords) : '';
	$pageurl = $fileurl.$keyurl;
	
	$where = !empty($keywords) ? "link_name like '%$keywords%'" : 1;
	$results = get_weblink_list($where, 'id', 'DESC', $start, $pagesize);
	$weblinks = array();
	foreach ($results as $row) {
		$row['deal_type'] = $deal_types[$row['deal_type']];
		$row['link_time'] = date('Y-m-d', $row['link_time']);
		$row['link_operate'] = '<a href="'.$fileurl.'?act=del&link_id='.$row['link_id'].'" onClick="return confirm(\'确认删除此内容吗？\');">删除</a>';
		$weblinks[] = $row;
	}
	
	$total = $DB->get_count($table, $where);
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
	
	$smarty->assign('keywords', $keywords);
	$smarty->assign('weblinks', $weblinks);
	$smarty->assign('showpage', $showpage);
	unset($results, $weblinks);
}

/** del */
if ($action == 'del') {
	$link_ids = (array) ($_POST['link_id'] ? $_POST['link_id'] : $_GET['link_id']);
	
	$DB->delete($table, 'link_id IN ('.dimplode($link_ids).')');
	unset($link_ids);
	
	msgbox('链接删除成功！', $fileurl);
}

smarty_output($tempfile);
?>