<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$user_email = $myself['user_email'];
$verify_code = random(32);

$active_link = HF_URL."?mod=activate&uid=$myself[user_id]&code=$verify_code";
//发送邮件
if (!empty($options['smtp_host']) && !empty($options['smtp_port']) && !empty($options['smtp_auth']) && !empty($options['smtp_user'])  && !empty($options['smtp_pass'])) {
	require(APP_PATH.'include/sendmail.php');
		
	$smarty->assign('site_name', $options['site_name']);
	$smarty->assign('site_url', $options['site_url']);
	$smarty->assign('user_email', $user_email);
	$smarty->assign('active_link', $active_link);
	$mailbody = $smarty->fetch('verify_mail.html');
	
	if (sendmail($user_email, '['.$options['site_name'].'] E-mail地址验证！', $mailbody)) {
		$DB->update($DB->table('users'), array('verify_code' => $verify_code, 'join_time' => time()), array('user_id' => $myself['user_id']));
		msgbox('验证邮件发送成功！', '?mod=home');
	} else {
		msgbox('邮件发送失败！请检查邮件发送功能设置是否正确或邮件地址错误！', '?mod=home');
	}
}
?>