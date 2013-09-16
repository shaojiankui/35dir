<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '找回密码';
$pageurl = '?mod=getpwd';
$tplfile = 'getpwd.html';
$table = $DB->table('users');

if (!$smarty->isCached($tplfile)) {
	$smarty->assign('pagename', $pagename);
	$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
	$smarty->assign('site_keywords', $options['site_keywords']);
	$smarty->assign('site_description', $options['site_description']);
	$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);
    
    if ($_POST['action'] == 'send') {
		$user_email = trim($_POST['email']);
		$check_code = strtolower(trim($_POST['code']));
		$post_time = time();
		$verify_code = random(32);
		
		if (empty($user_email) || !is_valid_email($user_email)) {
			msgbox('请输入有效的电子邮箱！');
		}
		
		if (empty($check_code) || $check_code != $_SESSION['code']) {
			unset($_SESSION['code']);
			msgbox('请输入正确的验证码！');	
		}
		
		$user = $DB->fetch_one("SELECT user_id, user_email, user_pass FROM $table WHERE user_email='$user_email'");
		if (!$user) {
			msgbox('您还不是本站的会员！');
		} else {
			$DB->update($table, array('verify_code' => $verify_code), array('user_id' => $user['user_id']));
			
			$reset_link = HF_URL."?mod=reset&uid=$user[user_id]&code=$verify_code";
			
			//发送邮件
			if (!empty($options['smtp_host']) && !empty($options['smtp_port']) && !empty($options['smtp_auth']) && !empty($options['smtp_user'])  && !empty($options['smtp_pass'])) {
				require(APP_PATH.'include/sendmail.php');
				
				$smarty->assign('site_name', $options['site_name']);
				$smarty->assign('site_url', $options['site_url']);
				$smarty->assign('user_email', $user['user_email']);
				$smarty->assign('post_time', date('Y-m-d H:i:s', $post_time));
				$smarty->assign('reset_link', $reset_link);
				$mailbody = $smarty->fetch('reset_mail.html');
				if (!sendmail($user_email, '['.$options['site_name'].'] 重置密码！', $mailbody)) {
					msgbox('邮件发送失败！请检查邮件发送功能设置是否正确或邮件地址错误！');
				}
			}
			unset($_SESSION['code']);
			
			msgbox('您好！已经将密码重置邮件发至<font color="#ff6600">'.$user['user_email'].'</font>邮箱中。');
		}
	}
}

smarty_output($tplfile);
?>