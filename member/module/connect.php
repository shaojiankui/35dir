<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

if ($options['is_enabled_connect'] == 'yes') {
	if (empty($options['qq_appid']) || empty($options['qq_appkey'])) {
		msgbox('QQ一键登录未正确设置，请设置后再使用！');	
	}
} else {
	msgbox('未开启QQ一键登录或注册功能！');
}

$table = $DB->table('users');

$oper = trim($_GET['oper']);
if (empty($oper)) $oper = 'init';

require(APP_PATH.'extend/connect/oauth_qq.php');

$config = array('appid' => $options['qq_appid'], 'appkey' => $options['qq_appkey'], 'callback' => HF_URL.'?mod=connect&oper=callback');
$oauth = oauth_qq::get_instance($config);
//login
if ($oper == 'init') $oauth->login();

//callback
if ($oper == 'callback') {
	$oauth->callback();
	$oauth->get_openid();
	$user = $oauth->get_user_info();
	
	$open_id = $_SESSION['openid'];
	if (empty($open_id)) {
		msgbox('QQ一键登录授权失败，请采用普通方式注册和登录！', '?mod=login');
	} else {
		$row = $DB->fetch_one("SELECT user_id, user_pass, login_time FROM $table WHERE open_id='$open_id'");
		if ($row) {
			$ip_address = sprintf("%u", ip2long(get_client_ip()));
        	$login_count = $row['login_count'] + 1;
					
			$data = array(
				'login_time' => time(),
				'login_ip' => $ip_address,
				'login_count' => $login_count,
			);
			$where = array('user_id' => $row['user_id']);
			$DB->update($table, $data, $where);
			
			$auth_cookie = authcode("$row[user_id]|$row[user_pass]|$login_count");
			$expire = time() + 3600 * 24;
			setcookie('auth_cookie', $auth_cookie, $expire, $options['site_root']);
				
			redirect('?mod=home');
		} else {
			require(APP_PATH.'module/user.php');
			/** check login  */
			$auth_cookie = $_COOKIE['auth_cookie'];
			$myself = check_user_login($auth_cookie);
			if (!empty($myself)) {
				$DB->update($table, array('open_id' => $openid), array('user_id' => $myself['user_id']));
			} else {
				$tplfile = 'connect.html';
				
				$smarty->assign('nick_name', $user['nickname']);
				$smarty->assign('open_id', $open_id);
				smarty_output($tplfile);
			}
		}
	}
}
?>