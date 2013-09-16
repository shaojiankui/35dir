<?php
require('common.php');
require(APP_PATH.'module/adver.php');

$fileurl = 'adver.php';
$tempfile = 'adver.html';
$table = $DB->table('advers');

if (!isset($action)) $action = 'list';

/** list */
if ($action == 'list') {
	$pagetitle = '广告列表';
	
	$adtype = intval($_GET['type']);
	$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));
	$pageurl = $fileurl.'?type='.$ad_type;
	$keyurl = !empty($keywords) ? '&keywords='.urlencode($keywords) : '';
	$pageurl .= $keyurl;
	
	switch ($adtype) {
		case 1 :
			$where = " adver_type=1";
			break;
		case 2 :
			$where = " adver_type=2";
			break;
		default :
			$where = " adver_type>-1";
			break;
	}
	$where .= !empty($keywords) ? " AND adver_name like '%$keywords%'" : "";
	$result = get_adver_list($where, 'adver_id', 'DESC', $start, $pagesize);
	$advers = array();
	foreach ($result as $row) {
		$row['adver_type'] = ($row['adver_type'] == 1) ? '<a href="'.$fileurl.'&type=1'.$keyurl.'">文字链接</a>' : '<a href="'.$fileurl.'&type=2'.$keyurl.'">广告代码</a>';
		if ($row['adver_days'] > 0) {
			$endtime = $row['adver_date'] + $row['adver_days'] * 24 * 3600;
			$row['adver_status'] = $endtime > $row['adver_date'] ? '<span class="gre">指定期限</span>' : '<span class="red">已过期</span>';
		} else {
			$row['adver_status'] = '<span class="org">长期有效</span>';
		}
		$row['adver_date'] = date('Y-m-d H:i:s', $endtime);
		$row['adver_operate'] = '<a href="'.$fileurl.'?act=edit&adver_id='.$row['adver_id'].'">编辑</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?act=del&adver_id='.$row['adver_id'].'" onClick="return confirm(\'确认删除此内容吗？\');">删除</a>';
		$advers[] = $row;
	}
		
	$total = $DB->get_count($table, $where);	
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
	
	$smarty->assign('keywords', $keywords);
	$smarty->assign('adtype_option', get_adtype_option($adtype));
	$smarty->assign('advers', $advers);
	$smarty->assign('showpage', $showpage);
	unset($result, $advers);
}

/** add */
if ($action == 'add') {
	$pagetitle = '添加新广告';
		
	$smarty->assign('ad_type', 1);
	$smarty->assign('h_action', 'saveadd');
}

/** edit */
if ($action == 'edit') {
	$pagetitle = '编辑广告';
	
	$adver_id = intval($_GET['adver_id']);
	$adver = get_one_adver($adver_id);
	if (!$adver) {
		msgbox('指定的内容不存在！');
	}
			
	$smarty->assign('ad_type', $adver['adver_type']);
	$smarty->assign('adver', $adver);
	$smarty->assign('h_action', 'saveedit');
}

/** save data */
if (in_array($action, array('saveadd', 'saveedit'))) {
	$adver_type = intval($_POST['adver_type']);
	$adver_name = trim($_POST['adver_name']);
	$adver_url = trim($_POST['adver_url']);
	$adver_code = trim($_POST['adver_code']);
	$adver_etips = trim($_POST['adver_etips']);
	$adver_days = intval($_POST['adver_days']);
	$adver_date = time();
	
	if (empty($adver_name)) {
		msgbox('请输入广告名称！');
	}
	
	if ($adver_type == 1) {
		if (empty($adver_url)) {
			msgbox('请输入广告地址！');
		} else {
			if (!is_valid_url($adver_url)) {
				msgbox('请输入正确的链接地址！');
			}
		}
	} elseif ($adver_type == 2) {
		if (empty($adver_code)) {
			msgbox('请输入广告代码！');
		}
	} else {
		msgbox('请选择广告类型！');
	}
	
	$data = array(
		'adver_type' => $adver_type,
		'adver_name' => $adver_name,
		'adver_url' => $adver_url,
		'adver_code' => $adver_code,
		'adver_etips' => $adver_etips,
		'adver_days' => $adver_days,
		'adver_date' => $adver_date,
	);
	
	if ($action == 'saveadd') {
    	$query = $DB->query("SELECT adver_id FROM $table WHERE adver_name='$adver_name'");
   		if ($DB->num_rows($query)) {
        	msgbox('您所添加的广告已存在！');
    	}
		
		$DB->insert($table, $data);
		update_cache('advers');
		
		msgbox('广告添加成功！', $fileurl.'?act=add');
	} elseif ($action == 'saveedit') {
		$adver_id = intval($_POST['adver_id']);
		$where = array('adver_id' => $adver_id);
		
		$DB->update($table, $data, $where);
		update_cache('advers');
		
		msgbox('广告修改成功！', $fileurl);
	}
}

/** del */
if ($action == 'del') {
	$adver_ids = (array) ($_POST['adver_id'] ? $_POST['adver_id'] : $_GET['adver_id']);
	
	$DB->delete($table, 'adver_id IN ('.dimplode($adver_ids).')');
	update_cache('advers');
	unset($adver_ids);
	
	msgbox('广告删除成功！', $fileurl);
}

smarty_output($tempfile);
?>