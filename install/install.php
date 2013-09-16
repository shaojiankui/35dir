<?php
error_reporting(0);

/** 设置时区 */
if (function_exists('date_default_timezone_set')) {
	date_default_timezone_set('PRC');
}

define('ROOT_PATH', str_replace("\\", '/', substr(__FILE__, 0, strrpos(dirname(__FILE__), DIRECTORY_SEPARATOR))).'/');

$php_self = htmlspecialchars($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
$site_url = htmlspecialchars('http://'.$_SERVER['HTTP_HOST'].substr($php_self, 0, strrpos($php_self, '/')).'/');

require('function.php');

//判断是否已经安装过
if (file_exists(ROOT_PATH.'data/install.lock')) {
	failure('你已经安装过本系统！<br />如果还继续安装，请先删除data/install.lock，再继续');
}

//数据库
$DB_TYPE = $_POST['DB_TYPE'];
$DB_HOST = $_POST['DB_HOST'];
$DB_PORT = $_POST['DB_PORT'];
$DB_NAME = $_POST['DB_NAME'];
$DB_USER = $_POST['DB_USER'];
$DB_PASS = $_POST['DB_PASS'];
$TABLE_PREFIX = $_POST['TABLE_PREFIX'];

//帐号
$email = $_POST['email'];
$pass = $_POST['pass'];

if (empty($DB_PORT)) $DB_PORT = 3306;
if (empty($TABLE_PREFIX)) $TABLE_PREFIX = 'dir_';
if (empty($DB_HOST)) failure('请填写数据库地址！');
if (empty($DB_NAME)) failure('请填写数据库名称！');
if (empty($DB_USER)) failure('请填写数据库账号！');
if (empty($email) || !is_valid_email($email)) failure('请填写有效的电子邮箱！');
if (empty($pass)) failure('请填写登录密码！');

$config = array(
	'DB_HOST' => $DB_HOST,
	'DB_NAME' => $DB_NAME,
	'DB_USER' => $DB_USER,
	'DB_PASS' => $DB_PASS,
	'TABLE_PREFIX' => $TABLE_PREFIX,
);

$db_link = mysql_connect($DB_HOST.':'.$DB_PORT, $DB_USER, $DB_PASS);
if (!$db_link) {
	failure('无法连接MySQL数据库，请检查数据库相关参数是否填写正确！');
}

//如果指定数据库不存在，则尝试创建
if (mysql_get_server_info() > '4.1') {
	mysql_query("CREATE DATABASE IF NOT EXISTS `".$DB_NAME."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
} else {
	mysql_query("CREATE DATABASE IF NOT EXISTS `".$DB_NAME."`");
}
	
//选择数据库
mysql_select_db($DB_NAME, $db_link);
	
//替换表前缀
$sql_array = replace_sql(ROOT_PATH.'data/sql/db.sql', 'dir_', $TABLE_PREFIX);
	
//执行数据库操作
foreach ($sql_array as $sql) {
	$query = mysql_query($sql, $db_link); //安装数据
	if (!$query) {
		failure('<strong>MySQL Error: </strong>'.mysql_error($db_link));
	}
}

//添加账号和密码
mysql_query("INSERT INTO `".$TABLE_PREFIX."users` (`user_type`, `user_email`, `user_pass`, `open_id`, `user_status`, `join_time`) VALUES
('admin', '$email', '".md5($pass)."', '', 3, '".time()."');", $db_link);
	
//修改配置文件
$config_file = ROOT_PATH.'config.php';
if (!set_config($config, $config_file)) {
	failure('配置文件写入失败！');
}

//安装成功，创建锁定文件
$data_dir = ROOT_PATH.'data/';
if (!is_dir($data_dir)) @mkdir($data_dir);
@fopen($data_dir.'install.lock', 'w');

//断开数据库链接
mysql_close($db_link);

//安装成功，跳转到首页
success('<strong>网站安装成功！</strong><br><br>您设置的管理员帐号和密码为：<br>帐号：'.$email.'<br>密码：'.$pass.'<br>');
?>