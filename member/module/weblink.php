<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

require(APP_PATH.'module/category.php');
require(APP_PATH.'module/weblink.php');

$pageurl = '?mod=weblink';
$tplfile = 'weblink.html';
$table = $DB->table('weblinks');

$action = isset($_GET['act']) ? $_GET['act'] : 'list';
$smarty->assign('action', $action); 

if (!$smarty->isCached($tplfile)) {
	/** list */
	if ($action == 'list') {
		$pagename = '链接管理';
		$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
		$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);
		
		$pagesize = 10;
		$curpage = intval($_GET['page']);
		if ($curpage > 1) {
			$start = ($curpage - 1) * $pagesize;
		} else {
			$start = 0;
			$curpage = 1;
		}
		
		$where = "l.user_id=".$myself['user_id'];
		$results = get_weblink_list($where, 'time', 'DESC', $start, $pagesize);
		$weblinks = array();
		foreach($results as $row) {
			if ($row['link_days'] > 0) {
				$endtime = $row['link_time'] + $row['link_days'] * 24 * 3600;
				$row['link_status'] = $endtime > $row['link_time'] ? '<span class="gre">'.$row['link_days'].'天后过期</span>' : '<span class="red">已过期</span>';
			} else {
				$row['link_status'] = '<span class="org">长期有效</span>';
			}
			
			$row['deal_type'] = $deal_types[$row['deal_type']];
			$row['link_price'] = ($row['link_price'] > 0 ? $row['link_price'] : '面议');
			$row['link_time'] = date('Y-m-d', $row['link_time']);
			$weblinks[] = $row;
		}
		$total = $DB->get_count($table.' l', $where);
		$showpage = showpage($pageurl, $total, $curpage, $pagesize);
		
		$smarty->assign('pagename', $pagename);
		$smarty->assign('weblinks', $weblinks);
		$smarty->assign('total', $total);
		$smarty->assign('showpage', $showpage);
	}
	
	/** add */
	if ($action == 'add') {
		$pagename = '发布链接';
		
		$smarty->assign('pagename', $pagename);
		$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
		$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);	
		$smarty->assign('weburl_option', get_weburl_option($myself['user_id']));
		$smarty->assign('dealtype_radio', get_dealtype_radio());
		$smarty->assign('linktype_radio', get_linktype_radio());
		$smarty->assign('linkpos_radio', get_linkpos_radio());
		
		$smarty->assign('do', 'saveadd');
	}
	
	/** edit */
	if ($action == 'edit') {
		$pagename = '链接编辑';
		
		$link_id = intval($_GET['lid']);
		$where = "l.user_id=$myself[user_id] AND l.link_id=$link_id";
		$row = get_one_weblink($where);
		if (!$row) {
			msgbox('您要修改的内容不存在或无权限！');
		}
		
		$smarty->assign('pagename', $pagename);
		$smarty->assign('site_title', $pagename.' - '.$options['site_title']);
		$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);	
		$smarty->assign('weburl_option', get_weburl_option($myself['user_id'], $row['web_id']));
		$smarty->assign('dealtype_radio', get_dealtype_radio($row['deal_type']));
		$smarty->assign('linktype_radio', get_linktype_radio($row['link_type']));
		$smarty->assign('linkpos_radio', get_linkpos_radio($row['link_pos']));
		$smarty->assign('row', $row);	
		$smarty->assign('do', 'saveedit');
	}
	
	/** del */
	if ($action == 'del') {
		$link_ids = (array) ($_POST['lid'] ? $_POST['lid'] : $_GET['lid']);
	
		$DB->delete($table, 'link_id IN ('.dimplode($link_ids).')');
		unset($link_ids);
	
		redirect($pageurl);
	}
	
	/** save */
	if (in_array($_POST['do'], array('saveadd', 'saveedit'))) {
		$web_id = intval($_POST['web_id']);
		$deal_type = intval($_POST['deal_type']);
		$link_name = trim($_POST['link_name']);
		$link_type = intval($_POST['link_type']);
		$link_pos = intval($_POST['link_pos']);
		$link_price = intval($_POST['link_price']);
		$link_if1 = trim($_POST['link_if1']);
		$link_if2 = trim($_POST['link_if2']);
		$link_if3 = trim($_POST['link_if3']);
		$link_if4 = trim($_POST['link_if4']);
		$link_intro = trim($_POST['link_intro']);
		$link_days = intval($_POST['link_days']);
		$link_time = time();
		
		if ($web_id <= 0) {
			msgbox('请选择站点！');
		}
		
		if (empty($link_name)) {
			msgbox('请输入链接名称！');
		} else {
			if (utf8_strlen($link_name) > 20) {
				msgbox('链接名称长度不能超过20个字符！');	
			}
			
			if (!censor_words($options['filter_words'], $link_name)) {
				msgbox('链接名称中含有非法关键词！');	
			}
		}
		
		$link_data = array(
			'user_id' => $myself['user_id'],
			'web_id' => $web_id,
			'deal_type' => $deal_type,
			'link_name' => $link_name,
			'link_type' => $link_type,
			'link_pos' => $link_pos,
			'link_price' => $link_price,
			'link_if1' => $link_if1,
			'link_if2' => $link_if2,
			'link_if3' => $link_if3,
			'link_if4' => $link_if4,
			'link_intro' => $link_intro,
			'link_days' => $link_days,
			'link_time' => $link_time,
		);
		
		if ($_POST['do'] == 'saveadd') {
    		$query = $DB->query("SELECT link_id FROM $table WHERE web_id='$web_id'");
    		if ($DB->num_rows($query)) {
        		msgbox('您所发布的链接已存在！');
    		}
			$DB->insert($table, $link_data);
			$insert_id = $DB->insert_id();
		
			msgbox('链接发布成功！', $pageurl);	
		} elseif ($_POST['do'] == 'saveedit') {
			$link_id = intval($_POST['link_id']);
			$where = array('link_id' => $link_id);
			$DB->update($table, $link_data, $where);
			
			msgbox('链接编辑成功！', $pageurl);
		}
	}
}

smarty_output($tplfile);
?>