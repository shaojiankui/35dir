<?php
define('IN_ADMIN', TRUE);
define('ROOT_PATH', str_replace('\\', '/', dirname(dirname(__FILE__))).'/');
define('APP_PATH', ROOT_PATH.'source/');

require(APP_PATH.'init.php');
require(APP_PATH.'module/static.php');
require('./function.php');

$pagesize = 30;
$curpage = intval($_GET['page']);
if ($curpage > 1) {
	$start = ($curpage - 1) * $pagesize;
} else {
	$start = 0;
	$curpage = 1;
}

$action = $_GET['act'] ? $_GET['act'] : $_POST['act'];

#category module
$category_modules = array('webdir' => '网站目录', 'article' => '文章资讯');
?>