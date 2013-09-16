<?php
require('common.php');
require(APP_PATH.'module/label.php');

$fileurl = 'label.php';
$tempfile = 'label.html';
$table = $DB->table('labels');

if (!isset($action)) $action = 'list';

/** list */
if ($action == 'list') {
	$pagetitle = '标签列表';

	$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));	
	$keyurl = !empty($keywords) ? '?keywords='.urlencode($keywords) : '';
	$pageurl = $fileurl.$keyurl;
	
	$where = !empty($keywords) ?  "label_name like '%$keywords%'" : 1;
	$result = get_label_list($where, 'label_id', 'DESC', $start, $pagesize);
	$labels = array();
	foreach ($result as $row) {
		$row['label_operate'] = '<a href="'.$fileurl.'?act=edit&label_id='.$row['label_id'].'">编辑</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?act=del&label_id='.$row['label_id'].'" onClick="return confirm(\'确认删除此内容吗？\');">删除</a>';
		$labels[] = $row;
	}
	
	$total = $DB->get_count($table, $where);	
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
	
	$smarty->assign('keywords', $keywords);
	$smarty->assign('labels', $labels);
	$smarty->assign('showpage', $showpage);
	unset($result, $labels);
}

/** add */
if ($action == 'add') {
	$pagetitle = '添加新标签';
			
	$smarty->assign('h_action', 'saveadd');
}

/** edit */
if ($action == 'edit') {
	$pagetitle = '编辑标签';
			
	$label_id = intval($_GET['label_id']);
	$label = get_one_label($label_id);
	if (!$label) {
		msgbox('指定的内容不存在！');
	}
	
	$smarty->assign('label', $label);			
	$smarty->assign('h_action', 'saveedit');
}

/** save data */
if (in_array($action, array('saveadd', 'saveedit'))) {
	$label_name = trim($_POST['label_name']);
	$label_intro = trim($_POST['label_intro']);
	$label_content = addslashes(trim($_POST['label_content']));
	
	if (empty($label_name)) {
		msgbox('请输入自定义标签名称！');
	} else {
		if (!is_valid_dir($label_name)) {
			msgbox('自定义标签名称只能是英文字母开头，数字，中划线，下划线组成！');
		}
	}
	
	if (empty($label_content)) {
		msgbox('请输入自定义标签内容！');
	}
	
	$data = array(
		'label_name' => $label_name,
		'label_intro' => $label_intro,
		'label_content' => $label_content,
	);
	
	if ($action == 'saveadd') {
    	$query = $DB->query("SELECT label_id FROM $table WHERE label_name='$label_name'");
    	if ($DB->num_rows($query)) {
        	msgbox('您所添加的标签已存在！');
    	}
		
		$DB->insert($table, $data);
		update_cache('labels');
		
		msgbox('自定义标签添加成功！', $fileurl.'?act=add');
	} elseif ($action == 'saveedit') {
		$label_id = intval($_POST['label_id']);
		$where = array('label_id' => $label_id);
		
		$DB->update($table, $data, $where);
		update_cache('labels');
		
		msgbox('自定义标签修改成功！', $fileurl);
	}
}

/** del */
if ($action == 'del') {
	$label_ids = (array) ($_POST['label_id'] ? $_POST['label_id'] : $_GET['label_id']);
	
	$DB->delete($table, 'label_id IN ('.dimplode($label_ids).')');
	update_cache('labels');
	unset($label_ids);
	
	msgbox('自定义标签删除成功！', $fileurl);
}

smarty_output($tempfile);
?>