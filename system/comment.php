<?php
require('common.php');

$fileurl = 'comment.php';
$tplfile = 'comment.html';
$table = $DB->table('comments');

if (!isset($action)) $action = 'list';

/** list */
if ($action == 'list') {
	$pagetitle = '站点列表';
	
	$status = $_GET['status'] ? intval($_GET['status']) : -1;
	$smarty->assign('status', $status);
	
	$pageurl = $fileurl.'?status='.$status;

	$where = '';
	$sql = "SELECT a.com_id, a.com_nick, a.com_email, a.com_text, a.com_ip, a.com_status, a.com_time, b.web_name, b.web_url FROM $table a LEFT JOIN ".$DB->table('websites')." b ON a.web_id=b.web_id WHERE";
	switch ($status) {
		case 0 :
			$where .= " a.com_status=0";
			break;
		case 1 :
			$where .= " a.com_status=1";
			break;
		default :
			$where .= " a.com_status>-1";
			break;
	}
	$sql .= $where." ORDER BY a.com_id DESC LIMIT $start, $pagesize";
	$query = $DB->query($sql);
	
	$comments = array();
	while ($row = $DB->fetch_array($query)) {
		$row['web_name'] = '<a href="'.format_url($row['web_url']).'" target="_blank">'.$row['web_name'].'</a>';
		$row['com_ip'] = long2ip($row['com_ip']);
		switch ($row['com_status']) {
			case 0 :
				$com_status = '<font color="#ff3300">待审核</font>';
				break;
			case 1 :
				$com_status = '<font color="#008800">已审核</font>';
				break;
		}
		$row['com_attr'] = $com_status;
		$row['com_time'] = date('Y-m-d H:i:s', $row['com_time']);
		$row['com_oper'] = '<a href="'.$fileurl.'?act=del&com_id='.$row['com_id'].'" onClick="return confirm(\'确认删除此内容吗？\');">删除</a>';
		$comments[] = $row;
	}
	$DB->free_result($query);
	
	$total = $DB->get_count($table.' a', $where);
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
	
	$smarty->assign('comments', $comments);
	$smarty->assign('showpage', $showpage);
}

/** del */
if ($action == 'del') {
	$com_ids = (array) ($_POST['com_id'] ? $_POST['com_id'] : $_GET['com_id']);
	
	$DB->delete($table, 'com_id IN ('.dimplode($com_ids).')');
	
	msgbox('评论删除成功！', $fileurl);
}

/** passed */
if ($action == 'passed') {
	$com_ids = (array) ($_POST['com_id'] ? $_POST['com_id'] : $_GET['com_id']);
	
	$DB->update($table, array('com_status' => 1), 'com_id IN ('.dimplode($com_ids).')');
	
	msgbox('评论审核成功！', $fileurl);
}

/** cancel */
if ($action == 'cancel') {
	$com_ids = (array) ($_POST['com_id'] ? $_POST['com_id'] : $_GET['com_id']);
	
	$DB->update($table, array('com_status' => 0), 'com_id IN ('.dimplode($com_ids).')');
	
	msgbox('评论取消审核成功！', $fileurl);
}

smarty_output($tplfile);
?>