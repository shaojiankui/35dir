<?php
error_reporting(0);
define('IN_HANFOX',true);
header('Content-type: text/html; charset=utf-8');
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('PRC'); }
ini_set('error_log',ROOT_PATH.'data/log/debug.log');

 ini_set('log_errors','1');
 
 @session_cache_limiter('private, must-revalidate');
 
 @session_start();
 
 @session_save_path(ROOT_PATH.'data/session');
 
 if (!file_exists(ROOT_PATH.'data/install.lock')) { header("Location: ./install/index.php\n"); exit; }
if (file_exists(ROOT_PATH.'config.php')) {
	require(ROOT_PATH.'config.php');
}else { exit('config.php file is missing!'); }
require(APP_PATH.'include/mysql.php');
require(APP_PATH.'include/smarty.php');
require(APP_PATH.'include/cache.php');
require(APP_PATH.'include/function.php');
require(APP_PATH.'include/validate.php');
require(APP_PATH.'version.php');
if (phpversion() <'5.3.0') { 
	set_magic_quotes_runtime(0);
	@ini_set('magic_quotes_sybase',0);
	}
	hf_magic_quotes();
	$DB = new MySQL(DB_HOST,DB_PORT,DB_USER,DB_PASS,DB_NAME,DB_CHARSET,TABLE_PREFIX,DB_PCONNECT); require(APP_PATH.'module/option.php');
	$options = get_options();
	$options = array_change_key_case($options,CASE_LOWER);
	if (substr($options['site_root'],-1) != '/') { $options['site_root'] .= '/'; }
	$php_self = htmlspecialchars($_SERVER['PHP_SELF'] ?$_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
	$base_name = basename($php_self); $site_root = substr($php_self,0,-strlen($base_name));
	$site_url = htmlspecialchars('http://'.$_SERVER['HTTP_HOST'].substr($php_self,0,strrpos($php_self,'/')).'/');
	$timescope = array('0'=>'所有时间内','1'=>'24小时内','3'=>'三天内','7'=>'一周内','30'=>'一月内','365'=>'一年内');
	$user_types = array('admin'=>'管理员','member'=>'注册会员','recruit'=>'快速收录','vip'=>'VIP会员');
	$deal_types = array('1'=>'出售','2'=>'交换'); $link_types = array('1'=>'文字','2'=>'图片');
	$link_pos = array('1'=>'首页','2'=>'内页','3'=>'全站'); define('HF_ROOT',$site_root);
	define('HF_URL',$site_url);

?>
