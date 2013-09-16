<?php
require('common.php');
require(APP_PATH.'module/category.php');
require(APP_PATH.'module/user.php');

$fileurl = 'user.php';
$tempfile = 'user.html';
$table = $DB->table('users');

if (!isset($action)) $action = 'list';

/** list */
if ($action == 'list') {
	$pagetitle = '会员列表';
	
	$user_type = trim($_GET['user_type']);
	$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));
	$keyurl = !empty($keywords) ? '?keywords='.urlencode($keywords) : '';
	$pageurl = $fileurl.$keyurl;
	
	$where = '1';
	$where .= !empty($user_type) ? " AND user_type='$user_type'" : " AND user_type != 'admin'";
	$where .= !empty($keywords) ? " AND user_email like '%$keywords%' OR nick_name like '%$keywords%'" : "";
	$result = get_user_list($where, 'join_time', 'DESC', $start, $pagesize);
	$users = array();
	foreach ($result as $row) {
		$row['user_type'] = $user_types[$row['user_type']];
		$row['join_time'] = date('Y-m-d H:i:s', $row['join_time']);
		$row['user_status'] = $row['user_status'] == 1 ? '<span class="gre">正常</span>' : '<span class="red">待验证</span>';
		$row['user_operate'] = '<a href="'.$fileurl.'?act=edit&user_id='.$row['user_id'].'">编辑</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?act=del&user_id='.$row['user_id'].'" onClick="return confirm(\'确认删除此内容吗？\');">删除</a>';
		$users[] = $row;
	}
	
	$total = $DB->get_count($table, $where);	
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
	
	$smarty->assign('usertype_option', get_usertype_option($user_type));
	$smarty->assign('user_type', $user_type);
	$smarty->assign('keywords', $keywords);
	$smarty->assign('users', $users);
	$smarty->assign('showpage', $showpage);
	unset($result, $users);
}

/** add */
if ($action == 'add') {
	$pagetitle = '添加会员';
	
	$smarty->assign('usertype_option', get_usertype_option());
	$smarty->assign('status', 1);
	$smarty->assign('h_action', 'saveadd');
}

/** edit */
if ($action == 'edit') {
	$pagetitle = '编辑会员';
	
	$user_id = intval($_GET['user_id']);
	$user = get_one_user($user_id);
	if (!$user) {
		msgbox('指定的会员不存在！');
	}
	
	$smarty->assign('usertype_option', get_usertype_option($user['user_type']));
	$smarty->assign('status', $user['user_status']);
	$smarty->assign('user', $user);
	$smarty->assign('h_action', 'saveedit');
}

/** save data */
if (in_array($action, array('saveadd', 'saveedit'))) {
	$user_type = trim($_POST['user_type']);
	$user_email = trim($_POST['user_email']);
	$user_pass = trim($_POST['user_pass']);
	$nick_name = trim($_POST['nick_name']);
	$user_qq = trim($_POST['user_qq']);
	$user_status = intval($_POST['user_status']);
	$join_time = time();
	
	if (empty($user_type)) {
		msgbox('请选择会员类型！');
	}
	
	if (empty($user_email)) {
		msgbox('请输入电子邮箱！');
	} else {
		if (!is_valid_email($user_email)) {
			msgbox('请输入正确的电子邮箱！');
		}
	}
	
	if (empty($user_pass)) {
		msgbox('请输入登录密码！');
	}	
	
	$data = array(
		'user_type' => $user_type,
		'user_email' => $user_email,
		'user_pass' => md5($user_pass),
		'nick_name' => $nick_name,
		'user_qq' => $user_qq,
		'user_status' => $user_status,
		'join_time' => $join_time,
	);
	
	if ($action == 'saveadd') {
    	$query = $DB->query("SELECT user_id FROM $table WHERE user_email='$user_email'");
    	if ($DB->num_rows($query)) {
        	msgbox('您所添加的会员已存在！');
    	}
		
		$DB->insert($table, $data);
		
		msgbox('会员添加成功！', $fileurl.'?act=add');	
	} elseif ($action == 'saveedit') {
		$user_id = intval($_POST['user_id']);
		$where = array('user_id' => $user_id);
		
		$DB->update($table, $data, $where);
		
		msgbox('会员编辑成功！', $fileurl);
	}
}

/** del */
if ($action == 'del') {
	$user_ids = (array) ($_POST['user_id'] ? $_POST['user_id'] : $_GET['user_id']);
	
	$DB->delete($table, 'user_id IN ('.dimplode($user_ids).')');
	unset($user_ids);
	
	msgbox('会员删除成功！', $fileurl);
}

/** set pass */
if ($action == 'setpass') {
	$user_ids = (array) ($_POST['user_id'] ? $_POST['user_id'] : $_GET['user_id']);
	
	$DB->update($table, array('user_status' => 1), 'user_id IN ('.dimplode($user_ids).')');
	unset($user_ids);
	
	msgbox('所选内容设置成功！', $fileurl);
}

/** del */
if ($action == 'nopass') {
	$user_ids = (array) ($_POST['user_id'] ? $_POST['user_id'] : $_GET['user_id']);
	
	$DB->update($table, array('user_status' => 0), 'user_id IN ('.dimplode($user_ids).')');
	unset($user_ids);
	
	msgbox('所选内容设置成功！', $fileurl);
}

smarty_output($tempfile);
?>