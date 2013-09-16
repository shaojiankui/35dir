<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '新会员注册';
$pageurl = '?mod=register';
$tplfile = 'register.html';
$table = $DB->table('users');

if ($options['is_enabled_register'] == 'no') {
	$msg = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>提示信息 - $options[site_name]</title>
<style type="text/css">
body {background: #f5f5f5;}
#msgbox {background: #fff; border: solid 3px #f1f1f1; font: normal 16px/30px normal; margin: 100px auto; padding: 100px 0; text-align: center; width: 500px;}
</style>
</head>

<body>
<div id="msgbox">抱歉，目前站点禁止新用户注册！<br /><a href="javascript:history.back();">[点击这里返回上一页]</a></div>
</body>
</html>
EOT;

	exit($msg);
}

if (!$smarty->isCached($tplfile)) {
	$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
	$smarty->assign('site_keywords', $options['site_keywords']);
	$smarty->assign('site_description', $options['site_description']);
	$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);
    
    if ($_POST['action'] == 'register') {
		$user_email = trim($_POST['email']);
		$user_pass = trim($_POST['pass']);
		$user_pass1 = trim($_POST['pass1']);
		$open_id = trim($_POST['open_id']);
		$nick_name = trim($_POST['nick']);
		$user_qq = trim($_POST['qq']);
		$check_code = strtolower(trim($_POST['code']));
		$post_time = time();
		$verify_code = random(32);
		
		if (empty($user_email) || !is_valid_email($user_email)) {
			msgbox('请输入正确的电子邮箱！');
		}
		
		if (empty($user_pass)) {
			msgbox('请输入帐号密码！');
		} else {
			if (strlen($user_pass) < 6 || strlen($user_pass) > 20) {
				msgbox('密码长度请保持在6-20个字符！');
			}
		}
		
		if (empty($user_pass1)) {
			msgbox('请输入确认密码！');
		}
		
		if ($user_pass != $user_pass1) {
			msgbox('两次密码输入不一致，请重新输入！');
		}
		
		if (empty($nick_name)) {
			msgbox('请输入昵称！');
		}
		
		if (empty($user_qq)) {
			msgbox('请输入腾讯QQ！');
		}
		
		if (empty($check_code) || $check_code != $_SESSION['code']) {
			unset($_SESSION['code']);
			msgbox('请输入正确的验证码！');
		}
		
		$query = $DB->query("SELECT user_id FROM $table WHERE user_email='$user_email'");
    	if ($DB->num_rows($query)) {
        	msgbox('该帐号已被注册！');
    	}
		
		if ($options['register_email_verify'] == 'yes') {
			$status = 0;
			$message = '马上去注册邮箱激活账号，完成最后一步，即刻享受'.$options['site_name'].'的各项服务。';
		} else {
			$status = 1;
			$message = '';
		}
		
		$user_data = array(
			'user_type' => 'member',
			'user_email' => $user_email,
			'user_pass' => md5($user_pass),
			'open_id' => $open_id,
			'nick_name' => $nick_name,
			'user_qq' => $user_qq,
			'verify_code' => $verify_code,
			'user_status' => $status,
			'join_time' => $post_time,
		);
		$DB->insert($table, $user_data);
		$uid = $DB->insert_id();
		
		if ($options['register_email_verify'] == 'yes') {
			$active_link = HF_URL."?mod=activate&uid=$uid&code=$verify_code";
			
			//发送邮件
			if (!empty($options['smtp_host']) && !empty($options['smtp_port']) && !empty($options['smtp_auth']) && !empty($options['smtp_user'])  && !empty($options['smtp_pass'])) {
				require(APP_PATH.'include/sendmail.php');
				
				$smarty->assign('site_name', $options['site_name']);
				$smarty->assign('site_url', $options['site_url']);
				$smarty->assign('user_email', $user_email);
				$smarty->assign('user_pass', $user_pass);
				$smarty->assign('post_time', date('Y-m-d H:i:s', $post_time));
				$smarty->assign('active_link', $active_link);
				$mailbody = $smarty->fetch('register_mail.html');
				if (!sendmail($user_email, '['.$options['site_name'].'] E-mail地址验证！', $mailbody)) {
					msgbox('邮件发送失败！请检查邮件发送功能设置是否正确或邮件地址错误！');	
				}
			}
		}
		unset($_SESSION['code']);
		
		msgbox('恭喜！您已注册成功！<br>'.$message, '?mod=login');
	}
}

smarty_output($tplfile);
?>