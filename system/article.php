<?php
require('common.php');
require(APP_PATH.'module/category.php');
require(APP_PATH.'module/article.php');
require(APP_PATH.'module/prelink.php');

$fileurl = 'article.php';
$tempfile = 'article.html';
$table = $DB->table('articles');

if (!isset($action)) $action = 'list';

/** list */
if ($action == 'list') {
	$pagetitle = '文章列表';
	
	$status = intval($_GET['status']);
	$cate_id = intval($_GET['cate_id']);
	$sort = intval($_GET['sort']);
	$order = strtoupper(trim($_GET['order']));
	$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));
	if (empty($order)) $order = 'DESC';
	
	$pageurl = $fileurl.'?status='.$status.'&cate_id='.$cate_id.'&sort='.$sort.'&order='.$order;
	$keyurl = !empty($keywords) ? '&keywords='.urlencode($keywords) : '';
	$pageurl .= $keyurl;
	
	$category_option = get_category_option('article', 0, $cate_id, 0);
	
	$smarty->assign('status', $status);
	$smarty->assign('cate_id', $cate_id);
	$smarty->assign('sort', $sort);
	$smarty->assign('order', $order);
	$smarty->assign('keywords', $keywords);
	$smarty->assign('keyurl', $keyurl);
	$smarty->assign('category_option', $category_option);
	
	$where = "";
	switch ($status) {
		case 1 :
			$where .= " a.art_status=1";
			break;
		case 2 :
			$where .= " a.art_status=2";
			break;
		case 3 :
			$where .= " a.art_status=3";
			break;
		default :
			$where .= " a.art_status>-1";
			break;
	}
	
	if ($cate_id > 0) {
		$cate = get_one_category($cate_id);
		$where .= " AND a.cate_id IN (".$cate['cate_arrchildid'].")";
	}
	
	if ($keywords) $where .= " AND a.art_title like '%$keywords%'";
	
	switch ($sort) {
		case 1 :
			$field = "a.art_ctime";
			break;
		case 2 :
			$field = "a.art_views";
			break;
		default :
			$field = "a.art_ctime";
			break;
	}
	
	$result = get_article_list($where, $field, $order, $start, $pagesize);
	$articles = array();
	foreach ($result as $row) {
		switch ($row['art_status']) {
			case 1 :
				$art_status = '<font color="#333333">草稿</font>';
				break;
			case 2 :
				$art_status = '<font color="#ff3300">待审核</font>';
				break;
			case 3 :
				$art_status = '<font color="#008800">已审核</font>';
				break;
		}
		$art_istop = $row['art_istop'] > 0 ? '<font color="#ff0000">置顶</font>' : '<font color="#cccccc">置顶</font>';
		$art_isbest = $row['art_isbest'] > 0 ? '<font color="#ff3300">推荐</font>' : '<font color="#cccccc">推荐</font>';
		$row['art_attr'] = $art_istop.' - '.$art_isbest.' - '.$art_status;
		$row['art_cate'] = '<a href="'.$fileurl.'?cate_id='.$row['cate_id'].'">'.get_category_name($row['cate_id']).'</a>';
		$row['art_operate'] = '<a href="'.$fileurl.'?act=edit&art_id='.$row['art_id'].'">编辑</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?act=del&art_id='.$row['art_id'].'" onClick="return confirm(\'确认删除此内容吗？\');">删除</a>';
		$articles[] = $row;
	}
	
	$total = $DB->get_count($table.' a', $where);	
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
	
	$smarty->assign('keywords', $keywords);
	$smarty->assign('articles', $articles);
	$smarty->assign('showpage', $showpage);
	unset($result, $articles);
}

/** add */
if ($action == 'add') {
	$pagetitle = '添加文章';

	$cate_id = intval($_GET['cate_id']);
	$category_option = get_category_option('article', 0, $cate_id, 0);
	
	$smarty->assign('category_option', $category_option);
	$smarty->assign('status', 3);
	$smarty->assign('h_action', 'saveadd');
}

/** edit */
if ($action == 'edit') {
	$pagetitle = '编辑文章';
	
	$art_id = intval($_GET['art_id']);
	$where = "a.art_id=$art_id";
	$row = get_one_article($where);
	if (!$row) {
		msgbox('指定的内容不存在！');
	}
	$category_option = get_category_option('article', 0, $row['cate_id'], 0);
	$row['art_content'] = str_replace('[upload_dir]', $options['site_root'].$options['upload_dir'].'/', $row['art_content']);
	
	$smarty->assign('category_option', $category_option);
	$smarty->assign('ispay', $row['art_ispay']);
	$smarty->assign('istop', $row['art_istop']);
	$smarty->assign('isbest', $row['art_isbest']);
	$smarty->assign('status', $row['art_status']);
	$smarty->assign('row', $row);
	$smarty->assign('h_action', 'saveedit');
}

/** move */
if ($action == 'move') {
	$pagetitle = '移动文章';
			
	$art_ids = (array) ($_POST['art_id'] ? $_POST['art_id'] : $_GET['art_id']);
	if (empty($art_ids)) {
		msgbox('请选择要移动的文章！');
	}
	$aids = dimplode($art_ids);
	
	$category_option = get_category_option('article', 0, 0, 0);
	$articles = $DB->fetch_all("SELECT art_id, art_title FROM $table WHERE art_id IN ($aids)");
	
	$smarty->assign('category_option', $category_option);
	$smarty->assign('articles', $articles);
	$smarty->assign('h_action', 'savemove');
}

/** attr */
if ($action == 'attr') {
	$pagetitle = '属性设置';
	
	$art_ids = (array) ($_POST['art_id'] ? $_POST['art_id'] : $_GET['art_id']);
	if (empty($art_ids)) {
		msgbox('请选择要设置的文章！');
	}	
	$aids = dimplode($art_ids);
	
	$category_option = get_category_option('article', 0, 0, 0);
	$articles = $DB->fetch_all("SELECT art_id, art_title FROM $table WHERE art_id IN ($aids)");
	
	$smarty->assign('category_option', $category_option);
	$smarty->assign('articles', $articles);
	$smarty->assign('h_action', 'saveattr');
}

/** save data */
if (in_array($action, array('saveadd', 'saveedit'))) {
	$cate_id = intval($_POST['cate_id']);
	$art_title = trim($_POST['art_title']);
	$art_tags = addslashes(trim($_POST['art_tags']));
	$copy_from = trim($_POST['copy_from']);
	$copy_url = trim($_POST['copy_url']);
	$art_intro = strip_tags(trim($_POST['art_intro']));
	$art_content = $_POST['art_content'];
	$art_views = intval($_POST['art_views']);
	$art_ispay = intval($_POST['art_ispay']);
	$art_istop = intval($_POST['art_istop']);
	$art_isbest = intval($_POST['art_isbest']);
	$art_status = intval($_POST['art_status']);
	$art_time = time();
	
	if ($cate_id <= 0) {
		msgbox('请选择文章所属分类！');
	} else {
		$row = get_one_category($cate_id);
		if ($row['cate_mod'] == 'article' && $row['cate_childcount'] > 0) {
			msgbox('指定的分类下有子分类，请选择子分类进行操作！');
		}
	}
	
	if (empty($art_title)) {
		msgbox('请输入文章标题！');
	}
	
	if (empty($art_tags)) {
		msgbox('请输入TAG标签！');
	} else {
		$art_tags = str_replace('|', ',', $art_tags);
		$art_tags = str_replace('、', ',', $art_tags);
		$art_tags = str_replace('，', ',', $art_tags);
		$art_tags = str_replace(',,', ',', $art_tags);
		if (substr($art_tags, -1) == ',') {
			$art_tags = substr($art_tags, 0, strlen($art_tags) - 1);
		}
	}
	
	if (empty($copy_from)) $copy_from = '本站原创';
	if (empty($copy_url)) $copy_url = $options['site_url'];
	
	if (empty($art_intro)) {
		msgbox('请输入内容摘要！');
	}
	
	$art_content = str_replace($options['site_root'].$options['upload_dir'].'/', '[upload_dir]', $art_content);
	
	$art_data = array(
		'cate_id' => $cate_id,
		'art_title' => $art_title,
		'art_tags' => $art_tags,
		'copy_from' => $copy_from,
		'copy_url' => $copy_url,
		'art_intro' => $art_intro,
		'art_content' => $art_content,
		'art_views' => $art_views,
		'art_ispay' => $art_ispay,
		'art_istop' => $art_istop,
		'art_isbest' => $art_isbest,
		'art_status' => $art_status,
		'art_ctime' => $art_time,
	);
	
	if ($action == 'saveadd') {
    	$query = $DB->query("SELECT art_id FROM $table WHERE art_title='$art_title'");
    	if ($DB->num_rows($query)) {
        	msgbox('您所添加的文章已存在！');
    	}
		
		$art_data['user_id'] = $myself['user_id'];
		$DB->insert($table, $art_data);
		$DB->query("UPDATE ".$DB->table('categories')." SET cate_postcount=cate_postcount+1 WHERE cate_mod='article' AND cate_id=$cate_id");
		
		msgbox('文章添加成功！', $fileurl.'?act=add&cate_id='.$cate_id);	
	} elseif ($action == 'saveedit') {
		$art_id = intval($_POST['art_id']);
		$where = array('art_id' => $art_id);
		unset($art_data['art_ctime']);
		
		$DB->update($table, $art_data, $where);
		$DB->query("UPDATE ".$DB->table('categories')." SET cate_postcount=cate_postcount+1 WHERE cate_mod='article' AND cate_id=$cate_id");
		
		msgbox('文章编辑成功！', $fileurl);
	}
}

/** del */
if ($action == 'del') {
	$art_ids = (array) ($_POST['art_id'] ? $_POST['art_id'] : $_GET['art_id']);
	
	$DB->delete($table, 'art_id IN ('.dimplode($art_ids).')');
	unset($art_ids);
	
	msgbox('文章删除成功！', $fileurl);
}

/** move */
if ($action == 'savemove') {
	$art_ids = (array) $_POST['art_id'];
	$cate_id = intval($_POST['cate_id']);
	if (empty($art_ids)) {
		msgbox('请选择要移动的内容！');
	}
	if ($cate_id <= 0) {
		msgbox('请选择分类！');
	} else {
		$cate = get_one_category($cate_id);
		if ($cate['cate_childcount'] > 0) {
			msgbox('指定的分类下有子分类，请选择子分类进行操作！');
		}
	}
	
	$DB->update($table, array('cate_id' => $cate_id), 'art_id IN ('.dimplode($art_ids).')');
	
	msgbox('文章移动成功！', $fileurl);
}

/** attr */
if ($action == 'saveattr') {
	$art_ids = (array) $_POST['art_id'];
	$art_ispay = intval($_POST['art_ispay']);
	$art_istop = intval($_POST['art_istop']);
	$art_isbest = intval($_POST['art_isbest']);
	$art_status = intval($_POST['art_status']);
	if (empty($art_ids)) {
		msgbox('请选择要设置的内容！');
	}
	
	$DB->update($table, array('art_ispay' => $art_ispay, 'art_istop' => $art_istop, 'art_isbest' => $art_isbest, 'art_status' => $art_status), 'art_id IN ('.dimplode($art_ids).')');
	
	msgbox('文章属性设置成功！', $fileurl);
}

smarty_output($tempfile);
?>