<?php
require('common.php');
require(APP_PATH.'module/category.php');
require(APP_PATH.'module/website.php');
require(APP_PATH.'module/webdata.php');

$fileurl = 'website.php';
$tempfile = 'website.html';
$table = $DB->table('websites');

if (!isset($action)) $action = 'list';

/** list */
if ($action == 'list') {
	$pagetitle = '站点列表';
	
	$status = intval(trim($_GET['status']));
	$cate_id = intval(trim($_GET['cate_id']));
	$sort = intval(trim($_GET['sort']));
	$order = strtoupper(trim($_GET['order']));
	$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));
	if (empty($order)) $order = 'DESC';
	
	$pageurl = $fileurl.'?status='.$status.'&cate_id='.$cate_id.'&sort='.$sort.'&order='.$order;
	$keyurl = !empty($keywords) ? '&keywords='.urlencode($keywords) : '';
	$pageurl .= $keyurl;
	
	$category_option = get_category_option('webdir', 0, $cate_id, 0);
	
	$smarty->assign('status', $status);
	$smarty->assign('cate_id', $cate_id);
	$smarty->assign('sort', $sort);
	$smarty->assign('order', $order);
	$smarty->assign('keywords', $keywords);
	$smarty->assign('keyurl', $keyurl);
	$smarty->assign('category_option', $category_option);
	
	$where = '';
	$sql = "SELECT a.web_id, a.cate_id, a.web_name, a.web_url, a.web_ispay, a.web_istop, a.web_isbest, a.web_islink, a.web_status, a.web_ctime, b.web_id, b.web_ip, b.web_grank, b.web_brank, b.web_srank, b.web_arank, b.web_instat, b.web_outstat, b.web_views, b.web_errors FROM $table a LEFT JOIN ".$DB->table('webdata')." b ON a.web_id=b.web_id WHERE";
	switch ($status) {
		case 1 :
			$where .= " a.web_status=1";
			break;
		case 2 :
			$where .= " a.web_status=2";
			break;
		case 3 :
			$where .= " a.web_status=3";
			break;
		default :
			$where .= " a.web_status>-1";
			break;
	}
	
	if ($cate_id > 0) {
		$cate = get_one_category($cate_id);
		$where .= " AND a.cate_id IN (".$cate['cate_arrchildid'].")";
	}
	
	if ($keywords) $where .= " AND a.web_name like '%$keywords%'";
	
	switch ($sort) {
		case 1 :
			$field = "a.web_ctime";
			break;
		case 2 :
			$field = "b.web_grank";
			break;
		case 3 :
			$field = "b.web_brank";
			break;
		case 4 :
			$field = "b.web_srank";
			break;
		case 5 :
			$field = "b.web_arank";
			break;
		case 6 :
			$field = "b.web_instat";
			break;
		case 7 :
			$field = "b.web_outstat";
			break;
		case 8 :
			$field = "b.web_views";
			break;
		case 9 :
			$field = "b.web_errors";
			break;
		default :
			$field = "a.web_ctime";
			break;
	}
	
	$sql .= $where." ORDER BY a.web_istop DESC, $field $order LIMIT $start, $pagesize";
	$query = $DB->query($sql);
	
	$websites = array();
	while ($web = $DB->fetch_array($query)) {
		switch ($web['web_status']) {
			case 1 :
				$web_status = '<font color="#333333">黑名单</font>';
				break;
			case 2 :
				$web_status = '<font color="#ff3300">待审核</font>';
				break;
			case 3 :
				$web_status = '<font color="#008800">已审核</font>';
				break;
		}
		$web_ispay = $web['web_ispay'] > 0 ? '<font color="#ff0000">付费</font>' : '<font color="#cccccc">付费</font>';
		$web_istop = $web['web_istop'] > 0 ? '<font color="#ff0000">置顶</font>' : '<font color="#cccccc">置顶</font>';
		$web_isbest = $web['web_isbest'] > 0 ? '<font color="#ff0000">推荐</font>' : '<font color="#cccccc">推荐</font>';
		$web_islink = $web['web_islink'] > 0 ? '<font color="#ff0000">未链接</font>' : '<font color="#cccccc">链接中</font>';
		$web['web_attr'] = $web_ispay.' - '.$web_istop.' - '.$web_isbest.' - '.$web_status.' - '.$web_islink;
		
		$web['web_cate'] = '<a href="'.$fileurl.'?cate_id='.$web['cate_id'].'">'.get_category_name($web['cate_id']).'</a>';
		$web['web_name'] = '<a href="'.format_url($web['web_url']).'" target="_blank">'.$web['web_name'].'</a> '.($web['web_errors'] > 0 ? '<sup style="color: #f00;">error!</sup>' : '');
		$web['web_ip'] = long2ip($web['web_ip']);
		$web['web_arank'] = number_format($web['web_arank']);
		$web['web_ctime'] = date('Y-m-d', $web['web_ctime']);
		$web['web_operate'] = '<a href="'.$fileurl.'?act=edit&web_id='.$web['web_id'].'">编辑</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?act=del&web_id='.$web['web_id'].'" onClick="return confirm(\'确认删除此内容吗？\');">删除</a>';
		$websites[] = $web;
	}
	unset($web);
	$DB->free_result($query);
	
	$total = $DB->get_count($table.' a', $where);
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
	
	$smarty->assign('websites', $websites);
	$smarty->assign('showpage', $showpage);
	unset($websites);
}

/** add */
if ($action == 'add') {
	$pagetitle = '添加站点';

	$cate_id = intval($_GET['cate_id']);
	$category_option = get_category_option('webdir', 0, $cate_id, 0);
	
	$smarty->assign('category_option', $category_option);
	$smarty->assign('status', 3);
	$smarty->assign('h_action', 'saveadd');
}

/** edit */
if ($action == 'edit') {
	$pagetitle = '编辑站点';
	
	$web_id = intval($_GET['web_id']);
	$where = "w.web_id=$web_id";
	$row = get_one_website($where);
	if (!$row) {
		msgbox('指定的内容不存在！');
	}
	$category_option = get_category_option('webdir', 0, $row['cate_id'], 0);
	
	$smarty->assign('category_option', $category_option);
	$smarty->assign('ispay', $row['web_ispay']);
	$smarty->assign('istop', $row['web_istop']);
	$smarty->assign('isbest', $row['web_isbest']);
	$smarty->assign('status', $row['web_status']);
	$smarty->assign('row', $row);
	$smarty->assign('h_action', 'saveedit');
}

/** move */
if ($action == 'move') {
	$pagetitle = '移动站点';
			
	$web_ids = (array) ($_POST['web_id'] ? $_POST['web_id'] : $_GET['web_id']);
	if (empty($web_ids)) {
		msgbox('请选择要移动的站点！');
	} else {
		$wids = dimplode($web_ids);
	}
	
	$category_option = get_category_option('webdir', 0, 0, 0);
	$websites = $DB->fetch_all("SELECT web_id, web_name FROM $table WHERE web_id IN ($wids)");
	
	$smarty->assign('category_option', $category_option);
	$smarty->assign('websites', $websites);
	$smarty->assign('h_action', 'savemove');
}

/** attr */
if ($action == 'attr') {
	$pagetitle = '属性设置';
	
	$web_ids = (array) ($_POST['web_id'] ? $_POST['web_id'] : $_GET['web_id']);
	if (empty($web_ids)) {
		msgbox('请选择要设置的站点！');
	} else {
		$wids = dimplode($web_ids);
	}
	
	$category_option = get_category_option('webdir', 0, 0, 0);
	$websites = $DB->fetch_all("SELECT web_id, web_name FROM $table WHERE web_id IN ($wids)");
	
	$smarty->assign('category_option', $category_option);
	$smarty->assign('websites', $websites);
	$smarty->assign('h_action', 'saveattr');
}

/** down */
if ($action == 'down') {
	$pagetitle = '下载站点图片';
}

/** save data */
if (in_array($action, array('saveadd', 'saveedit'))) {
	$cate_id = intval($_POST['cate_id']);
	$web_name = trim($_POST['web_name']);
	$web_url = trim($_POST['web_url']);
	$web_tags = strtolower(addslashes(trim($_POST['web_tags'])));
	$web_pic = trim($_POST['web_pic']);
	$web_intro = addslashes(trim($_POST['web_intro']));
	$web_ip = trim($_POST['web_ip']);
	$web_grank = intval($_POST['web_grank']);
	$web_brank = intval($_POST['web_brank']);
	$web_srank = intval($_POST['web_srank']);
	$web_arank = intval($_POST['web_arank']);
	$web_instat = intval($_POST['web_instat']);
	$web_outstat = intval($_POST['web_outstat']);
	$web_views = intval($_POST['web_views']);
	$web_errors = intval($_POST['web_errors']);
	$web_ispay = intval($_POST['web_ispay']);
	$web_istop = intval($_POST['web_istop']);
	$web_isbest = intval($_POST['web_isbest']);
	$web_status = intval($_POST['web_status']);
	$web_time = time();
	
	if ($cate_id <= 0) {
		msgbox('请选择网站所属分类！');
	} else {
		$row = get_one_category($cate_id);
		if ($row['cate_mod'] == 'website' && $row['cate_childcount'] > 0) {
			msgbox('指定的分类下有子分类，请选择子分类进行操作！');
		}
	}
	
	if (empty($web_name)) {
		msgbox('请输入网站名称！');
	}
	
	if (empty($web_url)) {
		msgbox('请输入网站域名！');
	} else {
		if (!is_valid_domain($web_url)) {
			msgbox('请输入正确的网站域名！');
		}
	}
	
	if (empty($web_intro)) {
		msgbox('请输入网站简介！');
	}
	
	// $web_url = str_replace('http://', '', $web_url);
	$web_tags = str_replace('，', ',', $web_tags);
	$web_tags = str_replace(',,', ',', $web_tags);
	if (substr($web_tags, -1) == ',') {
		$web_tags = substr($web_tags, 0, strlen($web_tags) - 1);
	}
	
	$web_ip = sprintf("%u", ip2long($web_ip));
	
	$web_data = array(
		'cate_id' => $cate_id,
		'web_name' => $web_name,
		'web_url' => $web_url,
		'web_tags' => $web_tags,
		'web_pic' => $web_pic,
		'web_intro' => $web_intro,
		'web_ispay' => $web_ispay,
		'web_istop' => $web_istop,
		'web_isbest' => $web_isbest,
		'web_status' => $web_status,
		'web_ctime' => $web_time,
	);
	
	$stat_data = array(
		'web_ip' => $web_ip,
		'web_grank' => $web_grank,
		'web_brank' => $web_brank,
		'web_srank' => $web_srank,
		'web_arank' => $web_arank,
		'web_instat' => $web_instat,
		'web_outstat' => $web_outstat,
		'web_views' => $web_views,
		'web_errors' => $web_errors,
		'web_utime' => $web_time,
	);
	
	if ($action == 'saveadd') {
    	$query = $DB->query("SELECT web_id FROM $table WHERE web_url='$web_url'");
    	if ($DB->num_rows($query)) {
        	msgbox('您所添加的网站已存在！');
    	}
		
		$web_data['user_id'] = $myself['user_id'];
		$DB->insert($table, $web_data);
		
		$stat_data['web_id'] = $DB->insert_id();
		$DB->insert($DB->table('webdata'), $stat_data);
		
		$DB->query("UPDATE ".$DB->table('categories')." SET cate_postcount=cate_postcount+1 WHERE cate_id=$cate_id");
		update_cache('archives');
		
		msgbox('网站添加成功！', $fileurl.'?act=add&cate_id='.$cate_id);	
	} elseif ($action == 'saveedit') {
		$web_id = intval($_POST['web_id']);
		$where = array('web_id' => $web_id);
		unset($web_data['web_ctime']);
		
		$DB->update($table, $web_data, $where);
		$DB->update($DB->table('webdata'), $stat_data, $where);
		
		$DB->query("UPDATE ".$DB->table('categories')." SET cate_postcount=cate_postcount+1 WHERE cate_id=$cate_id");
		update_cache('archives');
		
		msgbox('网站修改成功！', $fileurl);
	}
}

/** del */
if ($action == 'del') {
	$web_ids = (array) ($_POST['web_id'] ? $_POST['web_id'] : $_GET['web_id']);
	
	$DB->delete($table, 'web_id IN ('.dimplode($web_ids).')');
	$DB->delete($DB->table('webdata'), 'web_id IN ('.dimplode($web_ids).')');
	update_cache('archives');
	unset($web_ids);
	
	msgbox('网站删除成功！', $fileurl);
}

/** move */
if ($action == 'savemove') {
	$web_ids = (array) $_POST['web_id'];
	$cate_id = intval($_POST['cate_id']);
	if (empty($web_ids)) {
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
	
	$DB->update($table, array('cate_id' => $cate_id), 'web_id IN ('.dimplode($web_ids).')');
	update_cache('archives');
	
	msgbox('网站移动成功！', $fileurl);
}

/** attr */
if ($action == 'saveattr') {
	$web_ids = (array) $_POST['web_id'];
	$web_ispay = intval($_POST['web_ispay']);
	$web_istop = intval($_POST['web_istop']);
	$web_isbest = intval($_POST['web_isbest']);
	$web_status = intval($_POST['web_status']);
	if (empty($web_ids)) {
		msgbox('请选择要设置的内容！');
	}
	
	require(APP_PATH.'include/sendmail.php');
	require(APP_PATH.'module/prelink.php');
	
	$websites = $DB->fetch_all("SELECT w.web_id, w.web_name, u.user_email FROM $table w LEFT JOIN ".$DB->table("users")." u ON w.user_id=u.user_id WHERE w.web_id IN (".dimplode($web_ids).")");
	foreach ($websites as $row) {
		/*if ($web_status == 3) {
			$site_link = get_website_url($row['web_id'], true);
			//发送邮件
			if (!empty($options['smtp_host']) && !empty($options['smtp_port']) && !empty($options['smtp_auth']) && !empty($options['smtp_user'])  && !empty($options['smtp_pass'])) {	
				$smarty->assign('site_name', $options['site_name']);
				$smarty->assign('site_url', $options['site_url']);
				$smarty->assign('web_name', $row['web_name']);
				$smarty->assign('site_link', $site_link);
				$mailbody = $smarty->fetch('audit_mail.html');
				sendmail($row['user_email'], '['.$options['site_name'].'] 网站已通过审核！', $mailbody);
			}
		}*/
		$DB->update($table, array('web_ispay' => $web_ispay, 'web_istop' => $web_istop, 'web_isbest' => $web_isbest, 'web_status' => $web_status), array('web_id' => $row['web_id']));
	}
	
	//$DB->update($table, array('web_istop' => $web_istop, 'web_isbest' => $web_isbest, 'web_status' => $web_status), 'web_id IN ('.dimplode($web_ids).')');
	
	msgbox('网站属性设置成功！', $fileurl);
}

/** metainfo */
if ($action == 'metainfo') {
	$url = trim($_GET['url']);
	if (empty($url)) {
		exit('请输入网站域名！');
	} else {
		if (!is_valid_domain($url)) {
			exit('请输入正确的网站域名！');
		}
	}
	
	$meta = get_sitemeta($url);	
	echo '<script type="text/javascript">';
	echo '$("#web_name").attr("value", "'.$meta['title'].'");';
	echo '$("#web_tags").attr("value", "'.$meta['keywords'].'");';
	echo '$("#web_intro").attr("value", "'.$meta['description'].'");';
	echo '</script>';
	unset($meta);
}

/** webdata */
if ($action == 'webdata') {
	$url = trim($_GET['url']);
	if (empty($url)) {
		exit('请输入网站域名！');
	} else {
		if (!is_valid_domain($url)) {
			exit('请输入正确的网站域名！');
		}
	}
	
	$ip = get_serverip($url);
	$grank = get_pagerank($url);
	$brank = get_baidurank($url);
	$srank = get_sogourank($url);
	$arank = get_alexarank($url);
	echo '<script type="text/javascript">';
	echo '$("#web_ip").attr("value", "'.$ip.'");';
	echo '$("#web_grank").attr("value", "'.$grank.'");';
	echo '$("#web_brank").attr("value", "'.$brank.'");';
	echo '$("#web_srank").attr("value", "'.$srank.'");';
	echo '$("#web_arank").attr("value", "'.$arank.'");';
	echo '</script>';
}

smarty_output($tempfile);
?>