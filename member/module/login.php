<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '登录账户';
$pageurl = '?mod=login';
$tplfile = 'login.html';
$table = $DB->table('users');

if (!$smarty->isCached($tplfile)) {
	$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
	$smarty->assign('site_keywords', $options['site_keywords']);
	$smarty->assign('site_description', $options['site_description']);
	$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);
    
    if ($_POST['action'] == 'login') {
		$user_email = trim($_POST['email']);
		$user_pass = trim($_POST['pass']);
		$open_id = trim($_POST['open_id']);
		
		if (empty($user_email) || !is_valid_email($user_email)) {
			msgbox('请输入有效的电子邮箱！');
		}
        
		if (empty($user_pass)) {
			msgbox('请输入登陆密码！');
		}
		
		$newpass = md5($user_pass);
		$row = $DB->fetch_one("SELECT user_id, user_pass, login_time, login_count FROM $table WHERE user_email='$user_email'");
		if ($row) {
            if ($newpass == $row['user_pass']) {
				$ip_address = sprintf("%u", ip2long(get_client_ip()));
            	$login_count = $row['login_count'] + 1;
				
				$data = array(
					'open_id' => $open_id,
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
				msgbox('用户名或密码错误，请重试！');
			}			
		} else {
			msgbox('用户名或密码错误，请重试！');
		}
	}
}

smarty_output($tplfile);
?>