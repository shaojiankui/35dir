<?php
require('./load.php');

$pagetitle = SYS_NAME.SYS_VERSION;
$fileurl = 'login.php';
$tempfile = 'login.html';
$table = $DB->table('users');

if ($_POST['act'] == 'login') {
	$email = trim($_POST['email']);
	$pass = trim($_POST['pass']);
	
	if (empty($email) || !is_valid_email($email)) {
		msgbox('请输入有效电子邮件！');
	}
	if (empty($pass)) {
		msgbox('请输入登陆密码！');
	}
	
	$user = $DB->fetch_one("SELECT user_id, user_pass, login_count FROM $table WHERE user_type='admin' AND user_email='$email'");
	if ($user) {
		if ($user['user_id'] && $user['user_pass'] == md5($pass)) {
			$ip_address = sprintf("%u", ip2long(get_client_ip()));
			$login_count = $user['login_count'] + 1;
			$data = array(
				'login_time' => time(),
				'login_ip' => $ip_address,
				'login_count' => $login_count,
			);
			$where = array('user_id' => $user['user_id']);
			$DB->update($table, $data, $where);
			$user_auth = authcode("$user[user_id]@$user[user_pass]");
			setcookie('user_auth', $user_auth);
			
			redirect('admin.php');
		} else {
			msgbox('用户名或密码错误，请重试！');
		}
	} else {
		msgbox('用户名或密码错误，请重试！');
	}
}

if ($_GET['act'] == 'logout') {
	setcookie('user_auth', '');
	redirect('../');
}

smarty_output($tempfile);
?>