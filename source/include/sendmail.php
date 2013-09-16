<?php
function sendmail($mailto, $subject, $content) {
	global $options;
	
	require_once(APP_PATH.'extend/phpmailer/class.phpmailer.php');
    
	$mail = new PHPMailer();
	$mail->SMTPDebug = 1;
	$mail->IsSMTP(); //使用SMTP方式发送
	$mail->SMTPAuth = ($options['smtp_auth'] == 'yes') ? true : false; //启用SMTP验证功能，一般需要开启
	$mail->Host = $options['smtp_host']; //smtp服务器地址
	$mail->Port = $options['smtp_port']; //smtp服务器端口
	$mail->Username = $options['smtp_user']; //smtp服务器帐号
	$mail->Password = $options['smtp_pass']; // smtp服务器密码
	$mail->CharSet = 'utf-8'; //发送的邮件内容编码
	$mail->SetFrom($options['smtp_user'], $options['site_name']); //发件人的邮箱和姓名
	$mail->AddReplyTo($options['smtp_user'], $options['site_name']); //回复时的邮箱和姓名，一般跟发件人一样
	//$mail->SMTPSecure = "ssl"; //gmail需要启用sll安全连接
	$mail->Subject = $subject; //邮件主题
	$mail->MsgHTML($content); //邮件内容，支持html代码
	//发送邮件
	if (is_array($mailto)) {
		//同时发送给多个人
		foreach ($mailto as $key => $value) {
			$mail->AddAddress($value, "");  // 收件人邮箱和姓名
		}
	} else {		//只发送给一个人
		$mail->AddAddress($mailto, "");  // 收件人邮箱和姓名
	}
	
	if (!$mail->Send()) {
		//echo "Mailer Error: ".$mail->ErrorInfo;
		return false;
	} else {
		return true;
	}
}
?>