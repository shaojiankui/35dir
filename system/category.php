<?php
require('common.php');
require(APP_PATH.'module/category.php');

$fileurl = 'category.php';
$tempfile = 'category.html';
$table = $DB->table('categories');

$root_id = intval($_GET['root_id']);
$cate_mod = !empty($_GET['mod']) ? $_GET['mod'] : 'webdir';
$smarty->assign('root_id', $root_id);
$smarty->assign('cate_mod', $cate_mod);

if (!isset($action)) $action = 'list';

/** list */
if ($action == 'list') {
	$pagetitle = '分类列表';
	
	$sql = "SELECT * FROM $table WHERE root_id=$root_id AND cate_mod='$cate_mod' ORDER BY cate_order ASC";
	$query = $DB->query($sql);
	$categories = array();
	while ($row = $DB->fetch_array($query)) {
		$row['cate_mod'] = $category_modules[$row['cate_mod']];
		$cate_attr = empty($row['cate_url']) ? '<span class="gre">内部</span>' : '<span class="red">外部</span>';
		$cate_attr .= $row['cate_isbest'] != 0 ? ' - <span class="gre">推荐</span>' : '';
		$row['cate_attr'] = $cate_attr;
		$row['cate_operate'] = '<a href="'.$fileurl.'?mod='.$cate_mod.'&act=edit&cate_id='.$row['cate_id'].'">编辑</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?mod='.$cate_mod.'&act=clear&cate_id='.$row['cate_id'].'" onClick="return confirm(\'注：该操作将清空此分类及其子分类下的内容！\n\n确定清空吗？\');">清空</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?mod='.$cate_mod.'&act=del&cate_id='.$row['cate_id'].'" onClick="return confirm(\'注：该操作将同时删除此分类下的子分类及相关内容！\n\n确定删除吗？\');">删除</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?mod='.$cate_mod.'&root_id='.$row['cate_id'].'">进入子类</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?mod='.$cate_mod.'&act=add&root_id='.$row['cate_id'].'">添加子类</a>';
		$categories[] = $row;
	}
	unset($row);
	$DB->free_result($query);
	
	$smarty->assign('cate_mod', $cate_mod);
	$smarty->assign('categories', $categories);
	unset($categories);
}

/** add */
if ($action == 'add') {
	$pagetitle = '添加分类';
	
	$smarty->assign('category_option', get_category_option($cate_mod, 0, $root_id, 0));
	$smarty->assign('h_action', 'saveadd');
}

/** edit */
if ($action == 'edit') {
	$pagetitle = '编辑分类';
	
	$cate_id = intval($_GET['cate_id']);
	$row = get_one_category($cate_id);;
	if (!$row) {
		msgbox('指定的内容不存在！');
	}
	
	$smarty->assign('category_option', get_category_option($row['cate_mod'], 0, $row['root_id'], 0));
	$smarty->assign('row', $row);
	$smarty->assign('h_action', 'saveedit');
}

/** reset */
if ($action == 'reset') {
	$pagetitle = '复位分类';
	
	$smarty->assign('h_action', 'savereset');
}

/** merge */
if ($action == 'merge') {
	$pagetitle = '合并分类';
	$category_option = get_category_option($cate_mod, 0, 0, 0);
	
	$smarty->assign('category_option', $category_option);
	$smarty->assign('h_action', 'saveunite');
}

/** save data */
if (in_array($action, array('saveadd', 'saveedit'))) {
	$root_id = intval(trim($_POST['root_id']));
	$cate_mod = trim($_POST['cate_mod']);
	$cate_name = trim($_POST['cate_name']);
	$cate_dir = trim($_POST['cate_dir']);
	$cate_url = trim($_POST['cate_url']);
	$cate_isbest = intval($_POST['cate_isbest']);
	$cate_order = intval($_POST['cate_order']);
	$cate_keywords = trim($_POST['cate_keywords']);
	$cate_description = trim($_POST['cate_description']);
	
	if (empty($cate_mod)) $cate_mod = 'webdir';

	if (empty($cate_name)) {
		msgbox('请输入分类名称！');
	}
	
	if (!empty($cate_dir)) {
		if (!is_valid_dir($cate_dir)) {
			msgbox('目录名称只能是英文字母开头，数字，中划线，下划线组成！');
		}
	}
	
	$data = array(
		'root_id' => $root_id,
		'cate_mod' => $cate_mod,
		'cate_name' => $cate_name,
		'cate_dir' => $cate_dir,
		'cate_url' => $cate_url,
		'cate_isbest' => $cate_isbest,
		'cate_order' => $cate_order,
		'cate_keywords' => $cate_keywords,
		'cate_description' => $cate_description,
	);
	
	if ($action == 'saveadd') {
    	$query = $DB->query("SELECT cate_id FROM $table WHERE root_id='$root_id' AND cate_name='$cate_name'");
    	if ($DB->num_rows($query)) {
        	msgbox('您所添加的分类已存在！');
    	}
		$DB->insert($table, $data);
		update_categories();
		update_cache('categories');
		
		$fileurl = empty($root_id) ? $fileurl .= '?mod='.$cate_mod : $fileurl .= '?mod='.$cate_mod.'&root_id='.$root_id;
		redirect($fileurl);
	} elseif ($action == 'saveedit') {
		$cate_id = intval($_POST['cate_id']);
		$where = array('cate_id' => $cate_id);
		
		$DB->update($table, $data, $where);
		update_categories();
		update_cache('categories');
		
		$fileurl = empty($root_id) ? $fileurl .= '?mod='.$cate_mod : $fileurl .= '?mod='.$cate_mod.'&root_id='.$root_id;
		redirect($fileurl);
	}
}

/** del */
if ($action == 'del') {
	$cate_id = intval($_GET['cate_id']);
	
	$sql = "SELECT cate_arrchildid FROM $table WHERE cate_mod='$cate_mod' AND cate_id=$cate_id";
	$cate = $DB->fetch_one($sql);
	if (!$cate) {
		msgbox('指定的分类不存在！');
	} else {
		$child_cate = $cate['cate_arrchildid'];
	}
	
	$DB->delete($table, 'cate_id IN ('.$child_cate.')');
	$DB->delete($DB->table('websites'), 'cate_id IN ('.$child_cate.')');
	$DB->delete($DB->table('articles'), 'cate_id IN ('.$child_cate.')');
	update_categories();
	update_cache('categories');
	
	msgbox('分类删除成功！', $fileurl.'?mod='.$cate_mod);
}

/** clear */
if ($action == 'clear') {
	$cate_id = intval($_GET['cate_id']);
	
	$sql = "SELECT cate_arrchildid FROM $table WHERE cate_mod='$cate_mod' AND cate_id=$cate_id";
	$cate = $DB->fetch_one($sql);
	if (!$cate) {
		msgbox('指定的分类不存在！');
	} else {
		$child_cate = $cate['cate_arrchildid'];
	}
	
	if ($cate_mod == 'webdir') {
		$DB->delete($DB->table('websites'), 'cate_id IN ('.$child_cate.')');
	} else {
		$DB->delete($DB->table('articles'), 'cate_id IN ('.$child_cate.')');
	}
	update_categories();
	
	msgbox('指定分类下的内容已清空！', $fileurl.'?mod='.$cate_mod);
}

/** reset */
if ($action == 'savereset') {
	$cate_mod = trim($_POST['cate_mod']);
	$DB->update($table, array('root_id' => 0), array('cate_mod' => $cate_mod));
	update_categories();
	update_cache('categories');
	
	msgbox('分类复位成功，请重新对分类进行归属设置！', $fileurl);
}

/** unite */
if ($action == 'saveunite') {
	$source_id = (int) $_POST['source_id'];
	$target_id = (int) $_POST['target_id'];
	$cate_mod = trim($_POST['cate_mod']);
	
	if (empty($source_id)) {
		msgbox('请选择要合并的分类！');
	}
	
	if (empty($target_id)) {
		msgbox('请选择目标分类！');
	}
	
	if ($source_id == $target_id) {
		msgbox('请不要在相同的分类内操作！');
	}
	
	$sql = "SELECT cate_childcount FROM $table WHERE cate_mod='$cate_mod' AND cate_id=$target_id";
	$cate = $DB->fetch_one($sql);
	if (!$cate) {
		msgbox('指定的目标分类不存在！');
	} else {
		$target_child_count = $cate['cate_childcount'];
	}
	
	if ($target_child_count > 0) {
		msgbox('目标分类中含有子分类，不能进行操作！');
	}
	
	$DB->delete($table, array('cate_id' => $source_id, 'cate_mod' => $cate_mod));
	if ($cate_mod == 'webdir') {
		$DB->update($DB->table('websites'), array('cate_id' => $target_id), array('cate_id' => $source_id));
	} else {
		$DB->update($DB->table('articles'), array('cate_id' => $target_id), array('cate_id' => $source_id));
	}
	update_categories();
	update_cache('categories');
	
	msgbox('分类合并成功，且内容已转移到目标分类中！', $fileurl);
}

function update_categories() {
	global $DB, $table, $category;
	
	$sql = "SELECT cate_id, cate_mod FROM $table ORDER BY cate_id ASC";
	$cate_ids = $DB->fetch_all($sql);
	
	foreach ($cate_ids as $id) {
		$parent_id = get_category_parent_ids($id['cate_id']);
		$child_id = $id['cate_id'].get_category_child_ids($id['cate_id']);
		$child_count = get_category_count($id['cate_id']);
		if ($id['cate_mod'] == 'webdir') {
			$post_count = $DB->get_count($DB->table('websites'), 'cate_id IN ('.$child_id.')');
		} else {
			$post_count = $DB->get_count($DB->table('articles'), 'cate_id IN ('.$child_id.')');
		}
		
		$data = array(
			'cate_arrparentid' => $parent_id,
			'cate_arrchildid' => $child_id,
			'cate_childcount' => $child_count,
			'cate_postcount' => $post_count,
		);
		$where = array('cate_id' => $id['cate_id']);
		
		$DB->update($table, $data, $where);
	}
}

smarty_output($tempfile);
?>