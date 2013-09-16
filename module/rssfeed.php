<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$type = trim($_GET['type']);
$cate_id = intval($_GET['cid']);
if (empty($type)) $type = 'webdir';

if ($type == 'webdir') {
	get_website_rssfeed($cate_id);
} else {
	get_article_rssfeed($cate_id);
}
?>