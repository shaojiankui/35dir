<?php
require('common.php');
require(APP_PATH.'module/feedback.php');

$fileurl = 'feedback.php';
$tempfile = 'feedback.html';
$table = $DB->table('feedbacks');

if (!isset($action)) $action = 'list';

/** list */
if ($action == 'list') {
	$pagetitle = '意见反馈列表';
	
	$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));
	$keyurl = $keywords ? '?keywords='.urlencode($keywords) : '';
	$pageurl = $fileurl.$keyurl;
	
	$where = !empty($keywords) ? "fb_nick like '%$keywords%' OR fb_email like '%$keywords%'" : 1;
	$result = get_feedback_list($where, 'fb_id', 'DESC', $start, $pagesize);
	$feedback = array();
	foreach ($result as $row) {
		$row['fb_date'] = date('Y-m-d H:i:s', $row['fb_date']);
		$row['fb_operate'] = '<a href="'.$fileurl.'?act=view&fb_id='.$row['fb_id'].'">查看</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?act=del&fb_id='.$row['fb_id'].'" onClick="return confirm(\'确认删除此内容吗？\');">删除</a>';
		$feedback[] = $row;
	}
	
	$total = $DB->get_count($table, $where);
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
	
	$smarty->assign('keywords', $keywords);
	$smarty->assign('feedback', $feedback);
	$smarty->assign('showpage', $showpage);
	unset($result, $feedback);
}

/** view */
if ($action == 'view') {
	$pagetitle = '查看意见信息';
	
	$fb_id = intval($_GET['fb_id']);
	$fb = get_one_feedback($fb_id);
	if (!$fb) {
		msgbox('指定的内容不存在！');
	}
			
	$fb['fb_date'] = date('Y-m-d H:i:s', $fb['fb_date']);
	$smarty->assign('fb', $fb);
}

/** del */
if ($action == 'del') {
	$fb_ids = (array) ($_POST['fb_id'] ? $_POST['fb_id'] : $_GET['fb_id']);
	
	$DB->delete($table, 'fb_id IN ('.dimplode($fb_ids).')');
	unset($fb_ids);
	
	msgbox('信息删除成功！', $fileurl);
}

smarty_output($tempfile);
?>