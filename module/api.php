<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$cate_id = intval($_GET['cid']);

$pagesize = 10;
$curpage = intval($_GET['page']);
if ($curpage > 1) {
	$start = ($curpage - 1) * $pagesize;
} else {
	$start = 0;
	$curpage = 1;
}

get_website_api($cate_id, $start, $pagesize);
?>