<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

require(APP_PATH.'module/category.php');
require(APP_PATH.'module/article.php');

$pageurl = '?mod=article';
$tplfile = 'article.html';
$table = $DB->table('articles');

$action = isset($_GET['act']) ? $_GET['act'] : 'list';
$smarty->assign('action', $action); 

if (!$smarty->isCached($tplfile)) {
	/** list */
	if ($action == 'list') {
		$pagename = '文章管理';
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
		
		$where = "a.user_id=".$myself['user_id'];
	
		$articles = get_article_list($where, 'ctime', 'DESC', $start, $pagesize);
		$total = $DB->get_count($table.' a', $where);
		$showpage = showpage($pageurl, $total, $curpage, $pagesize);
		
		$smarty->assign('pagename', $pagename);
		$smarty->assign('articles', $articles);
		$smarty->assign('total', $total);
		$smarty->assign('showpage', $showpage);
	}
	
	/** add */
	if ($action == 'add') {
		$pagename = '发布文章';
		
		$smarty->assign('pagename', $pagename);
		$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
		$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);	
		$smarty->assign('category_option', get_category_option('article', 0, 0, 0));	
		$smarty->assign('do', 'saveadd');
	}
	
	/** edit */
	if ($action == 'edit') {
		$pagename = '编辑文章';
		
		$art_id = intval($_GET['aid']);
		$where = "a.user_id=$myself[user_id] AND a.art_id=$art_id";
		$row = get_one_article($where);
		if (!$row) {
			msgbox('您要修改的内容不存在或无权限！');
		}
		$row['art_content'] = str_replace('[upload_dir]', $options['site_root'].$options['upload_dir'].'/', $row['art_content']);
		
		$smarty->assign('pagename', $pagename);
		$smarty->assign('site_title', $pagename.' - '.$options['site_title']);
		$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);	
		$smarty->assign('category_option', get_category_option('article', 0, $row['cate_id'], 0));
		$smarty->assign('row', $row);
		$smarty->assign('do', 'saveedit');
	}
	
	/** save */
	if (in_array($_POST['do'], array('saveadd', 'saveedit'))) {
		$cate_id = intval($_POST['cate_id']);
		$art_title = trim($_POST['art_title']);
		$art_tags = addslashes(trim($_POST['art_tags']));
		$copy_from = trim($_POST['copy_from']);
		$copy_url = trim($_POST['copy_url']);
		$art_intro = strip_tags(trim($_POST['art_intro']));
		$art_content = $_POST['art_content'];
		$art_time = time();
		
		if ($cate_id <= 0) {
			msgbox('请选择文章所属分类！');
		} else {
			$cate = get_one_category($cate_id);
			if ($cate['cate_childcount'] > 0) {
				msgbox('指定的分类下有子分类，请选择子分类进行操作！');
			}
		}
		
		if (empty($art_title)) {
			msgbox('请输入文章标题！');
		} else {
			if (!censor_words($options['filter_words'], $art_title)) {
				msgbox('文章标题中含有非法关键词！');	
			}
		}
		
		if (empty($art_tags)) {
			msgbox('请输入TAG标签！');
		} else {
			if (!censor_words($options['filter_words'], $art_tags)) {
				msgbox('TAG标签中含有非法关键词！');
			}
			
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
		} else {
			if (!censor_words($options['filter_words'], $art_intro)) {
				msgbox('内容摘要中含有非法关键词！');	
			}
		}
		
		if (empty($art_content)) {
			msgbox('请输入文章内容！');
		} else {
			if (!censor_words($options['filter_words'], $art_content)) {
				msgbox('文章内容中含有非法关键词！');	
			}
		}
		
		$art_content = str_replace($options['site_root'].$options['upload_dir'].'/', '[upload_dir]', $art_content);
		
		$art_data = array(
			'user_id' => $myself['user_id'],
			'cate_id' => $cate_id,
			'art_title' => $art_title,
			'art_tags' => $art_tags,
			'copy_from' => $copy_from,
			'copy_url' => $copy_url,
			'art_intro' => $art_intro,
			'art_content' => $art_content,
			'art_status' => 2,
			'art_ctime' => $art_time,
		);
		
		if ($_POST['do'] == 'saveadd') {
    		$query = $DB->query("SELECT art_id FROM $table WHERE art_title='$art_title'");
    		if ($DB->num_rows($query)) {
        		msgbox('您所发布的文章已存在！');
    		}
			$DB->insert($table, $art_data);
			$insert_id = $DB->insert_id();
		
			msgbox('文章发布成功！', $pageurl);	
		} elseif ($_POST['do'] == 'saveedit') {
			$art_id = intval($_POST['art_id']);
			$where = array('art_id' => $art_id);
			$DB->update($table, $art_data, $where);
			
			msgbox('文章编辑成功！', $pageurl);
		}
	}
}

smarty_output($tplfile);
?>