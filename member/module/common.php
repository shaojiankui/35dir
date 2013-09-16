<?php
function smarty_output($template, $cache_id = NULL, $compile_id = NULL) {
	global $smarty, $options;
	
	template_exists($template);
	
	$options = stripslashes_deep($options);
	
	$smarty->assign('site_root', $options['site_root']);
	$smarty->assign('site_name', $options['site_name']);
	$smarty->assign('site_url', $options['site_url']);
	$smarty->assign('site_copyright', $options['site_copyright']);
	$smarty->assign('cfg', $options); #options
	$smarty->display($template, $cache_id, $compile_id);
	
	$content = ob_get_contents();
	if ($options['link_struct'] != 0) {
		$content = rewrite_output($content);
	}
	
	ob_end_clean();
	$options['is_enabled_gzip'] == 'yes' ? ob_start('ob_gzhandler') : ob_start();
	
	unset($options);
	echo $content;
}


function msgbox($msg, $url = 'javascript: history.go(-1);') {
	global $smarty;
	
	$template = 'msgbox.html';
	template_exists($template);
	
	$smarty->assign('msg', $msg);
	$smarty->assign('url', $url);
	echo $smarty->fetch('msgbox.html');
	@ob_end_flush();
	exit();
}

function redirect($url) {
    header('location:'.$url, false, 301);
	exit;
}

function get_scripttime() {
	global $DB, $options, $start_time;
	
	$mtime = explode(' ', microtime());
	$end_time = $mtime[1] + $mtime[0];
	$exec_time = number_format(($end_time - $start_time), 6);
	$gzip = $options['is_enabled_gzip'] ? 'Enabled' : 'Disabled';
	
	return 'Processed in '.$exec_time.' second(s), '.$DB->queries.' Queries, Gzip '.$gzip;
}

function insert_script_time() {
	return get_scripttime();
}
	
/** site path */
function get_sitepath() {
	global $options;
	
	$strpath = '当前位置：<a href="'.$options['site_url'].'">'.$options['site_name'].'</a> &raquo; <a href="'.$options['site_root'].'member/?mod=home">会员中心</a>';
	
	return $strpath;
}
?>