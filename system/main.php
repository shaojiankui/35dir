<?php
require('common.php');
require(APP_PATH.'module/stats.php');

$pagetitle = '管理首页';
$tempfile = 'main.html';

$server = array();
$server['datetime'] = date('Y-m-d　H:i:s');
$server['software'] = $_SERVER['SERVER_SOFTWARE'];
$server['php_version'] = PHP_VERSION;
$server['mysql_version'] = $DB->version();
$server['smarty_version'] = Smarty::SMARTY_VERSION;
$server['soft_version'] = SYS_VERSION.' Build '.SYS_RELEASE;

$server['globals'] = get_phpcfg('register_globals');
$server['safemode'] = get_phpcfg('safe_mode');
$server['rewrite'] = apache_mod_enabled('mod_rewrite') ? '<font color="#008800">√</font>' : '<font color="#FF0000">×</font>';
if (function_exists('memory_get_usage')) {
	$server['memory_info'] = get_real_size(memory_get_usage());
}

function get_phpcfg($varname) {
	switch ($result = get_cfg_var($varname)) {
		case 0 :
			return '<font color="#FF0000">×</font>';
			break;
		case 1 :
			return '<font color="#008800">√</font>';
			break;
		default :
			return $result;
			break;
	}
}

$smarty->assign('login_user', $login_user);
$smarty->assign('login_time', $login_time);
$smarty->assign('login_ip', $login_ip);
$smarty->assign('login_count', $login_count);
$smarty->assign('server', $server);
$smarty->assign('stat', get_stats());
unset($server);

smarty_output($tempfile);
?>