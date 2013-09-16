<?php
require('./load.php');

$table = $DB->table('users');
$user_auth = $_COOKIE['user_auth'];
list($user_id, $user_pass) = $user_auth ? explode('@', authcode($user_auth, 'DECODE')) : array('', '', '');
$user_id = intval($user_id);
$user_pass = addslashes($user_pass);
if (!$user_id || !$user_pass) {
	$user_id = 0;	
	msgbox('您还未登录或无权限！', './login.php');
}

$myself = array();
$user = $DB->fetch_one("SELECT user_id, user_email, user_pass, login_time, login_ip, login_count FROM $table WHERE user_type='admin' AND user_id='$user_id'");
if (!$user) {
	$myself = array();
	setcookie('user_auth', '');
	msgbox('您还未登录或无权限！', './login.php');
} else {
	if ($user['user_pass'] == $user_pass) {
		$myself = array(
			'user_id' => $user['user_id'],
			'user_email' => $user['user_email'],
			'login_time' => date('Y-m-d H:i:s', $user['login_time']),
			'login_ip' => long2ip($user['login_ip']),
			'login_count' => $user['login_count'],
		);
	}
}

if (empty($myself)) {
	msgbox('您还未登录或无权限！', './login.php');
}

$smarty->assign('myself', $myself);
?>