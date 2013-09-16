<?php
/** rewrite output */
function rewrite_output($content) {
	$search = array(
		"/href\=\"(\.*\/*)\?mod\=(index|webdir|weblink|article|category|update|archives|top|feedback|link)?\"/e",
		"/href\=\"(\.*\/*)\?mod\=webdir([&amp;|&]cid\=(\d+))?([&amp;|&]page\=(\d+))?\"/e",
		"/href\=\"(\.*\/*)\?mod\=article([&amp;|&]cid\=(\d+))?([&amp;|&]page\=(\d+))?\"/e",
		"/href\=\"(\.*\/*)\?mod\=update([&amp;|&]days\=(\d+))?([&amp;|&]page\=(\d+))?\"/e",
		"/href\=\"(\.*\/*)\?mod\=archives([&amp;|&]date\=(\d+))?([&amp;|&]page\=(\d+))?\"/e",
		"/href\=\"(\.*\/*)\?mod\=search([&amp;|&]type\=(.+?))?([&amp;|&]query\=(.+?))?([&amp;|&]page\=(\d+))?\"/e",
		"/href\=\"(\.*\/*)\?mod\=siteinfo[&amp;|&]wid\=(\d+)\"/e",
		"/href\=\"(\.*\/*)\?mod\=diypage[&amp;|&]pid\=(\d+)\"/e",
		"/href\=\"(\.*\/*)\?mod\=rssfeed([&amp;|&]type\=(webdir|article))?([&amp;|&]cid\=(\d+))?\"/e",
		"/href\=\"(\.*\/*)\?mod\=sitemap([&amp;|&]type\=(webdir|article))?([&amp;|&]cid\=(\d+))?\"/e",
	);
		
	$replace = array(
		"rewrite_module('\\2')",
		"rewrite_category('webdir', '\\3', '\\5')",
		"rewrite_category('article', '\\3', '\\5')",
		"rewrite_update('\\3', '\\5')",		
		"rewrite_archives('\\3', '\\5')",
		"rewrite_search('\\3', '\\5', '\\7')",
		"rewrite_siteinfo('\\2')",
		"rewrite_diypage('\\2')",
		"rewrite_rssfeed('\\3', '\\5')",
		"rewrite_sitemap('\\3', '\\5')",
	);
	
	return preg_replace($search, $replace, $content);
}

/** module */
function rewrite_module($module) {	
	return 'href="'.get_module_url($module).'"';
}

/** category */
function rewrite_category($cate_mod, $cate_id, $page) {
	return 'href="'.get_category_url($cate_mod, $cate_id, $page).'"';
}

/** update */
function rewrite_update($days, $page) {
	return 'href="'.get_update_url($days, $page).'"';
}

/** archives */
function rewrite_archives($date, $page) {
	return 'href="'.get_archives_url($date, $page).'"';
}
	
/** search */
function rewrite_search($type = 'name', $query, $page) {
	return 'href="'.get_search_url($type, $query, $page).'"';
}

/** siteinfo */
function rewrite_siteinfo($web_id) {
	return 'href="'.get_website_url($web_id).'"';
}

/** diypage */
function rewrite_diypage($page_id) {	
	return 'href="'.get_diypage_url($page_id).'"';
}

/** rssfeed */
function rewrite_rssfeed($module, $cate_id) {
	return 'href="'.get_rssfeed_url($module, $cate_id).'"';
}

/** sitemap */
function rewrite_sitemap($module, $cate_id) {
	return 'href="'.get_sitemap_url($module, $cate_id).'"';
}
?>