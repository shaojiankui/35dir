<?php
require('common.php');
require(APP_PATH.'module/link.php');

$fileurl = 'link.php';
$tempfile = 'link.html';
$table = $DB->table('links');

if (!isset($action)) $action = 'list';

/** list */
if ($action == 'list') {
	$pagetitle = '链接列表';
	
	$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));
	$keyurl = !empty($keywords) ? '?keywords='.urlencode($keywords) : '';
	$pageurl = $fileurl.$keyurl;
	
	$where = !empty($keywords) ? "link_name like '%$keywords%' OR link_url like '%$keywords%'" : 1;
	$result = get_link_list($where, 'link_id', 'DESC', $start, $pagesize);
	$links = array();
	foreach ($result as $row) {
		$row['link_url'] = '<a href="'.$row['link_url'].'" target="_blank">'.$row['link_url'].'</a>';
		$row['link_hide'] = $row['link_hide'] == 1 ? '<span class="gre">显示</span>' : '<span class="red">隐藏</span>';
		$row['link_operate'] = '<a href="'.$fileurl.'?act=edit&link_id='.$row['link_id'].'">编辑</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?act=del&link_id='.$row['link_id'].'" onClick="return confirm(\'确认删除此内容吗？\');">删除</a>';
		$links[] = $row;
	}
	
	$total = $DB->get_count($table, $where);	
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
	
	$smarty->assign('keywords', $keywords);
	$smarty->assign('links', $links);
	$smarty->assign('showpage', $showpage);
	unset($result, $links);
}

/** add */
if ($action == 'add') {
	$pagetitle = '添加新链接';
	
	$smarty->assign('display', 1);
	$smarty->assign('h_action', 'saveadd');
}

/** edit */
if ($action == 'edit') {
	$pagetitle = '编辑链接';
			
	$link_id = intval($_GET['link_id']);
	$link = get_one_link($link_id);
	if (!$link) {
		msgbox('指定的内容不存在！');
	}
	
	$smarty->assign('display', $link['link_hide']);
	$smarty->assign('link', $link);
	$smarty->assign('h_action', 'saveedit');
}

/** save data */
if (in_array($action, array('saveadd', 'saveedit'))) {
	$link_name = trim($_POST['link_name']);
	$link_url = trim($_POST['link_url']);
	$link_logo = trim($_POST['link_logo']);
	$link_hide = intval($_POST['link_hide']);
	$link_order = intval($_POST['link_order']);
	
	if (empty($link_name)) {
		msgbox('请输入链接名称！');
	}
	
	if (empty($link_url)) {
		msgbox('请输入链接地址！');
	} else {
		if (!is_valid_url($link_url)) {
			msgbox('请输入正确的链接地址！');
		}
	}

	$data = array(
		'link_name' => $link_name,
		'link_url' => $link_url,
		'link_logo' => $link_logo,
		'link_hide' => $link_hide,
		'link_order' => $link_order,
	);
	
	if ($action == 'saveadd') {
		$query = $DB->query("SELECT link_id FROM $table WHERE link_name='$link_name' AND link_url='$link_url'");
		if ($DB->num_rows($query)) {
			msgbox('您所添加的链接已存在！');
		}
		
		$DB->insert($table, $data);
		update_cache('links');
		
		msgbox('链接添加成功！', $fileurl.'?act=add');
	} elseif ($action == 'saveedit') {
		$link_id = intval($_POST['link_id']);
		$where = array('link_id' => $link_id);
		
		$DB->update($table, $data, $where);
		update_cache('links');
		
		msgbox('链接修改成功！', $fileurl);
	}
}

/** del */
if ($action == 'del') {
	$link_ids = (array) ($_POST['link_id'] ? $_POST['link_id'] : $_GET['link_id']);
	
	$DB->delete($table, 'link_id IN ('.dimplode($link_ids).')');
	update_cache('links');
	unset($link_ids);
	
	msgbox('链接删除成功！', $fileurl);
}

smarty_output($tempfile);
?>