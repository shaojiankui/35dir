<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$type = trim($_POST['type']);
/** comment */
if ($type == 'comment') {
	$rid = intval($_POST['rid']);
	$wid = intval($_POST['wid']);
	$nick = trim($_POST['nick']);
	$email = trim($_POST['email']);
	$content = strip_tags($_POST['content']);
	$ipaddr = sprintf("%u", ip2long(get_client_ip()));
	$time = time();
	
	if ($wid <= 0) {
		echo('丢失参数！');	
		exit();
	}	
	
	if (empty($nick)) {
		echo('昵称不能为空！');	
		exit();
	}
	
	if (empty($email)) {
		echo('Email不能为空！');
		exit();
	} else {
		if (!is_valid_email($email)) {
			echo('Email格式不正确！');
			exit();
		}
	}
	
	if (empty($content)) {
		echo('内容不能为空！');	
		exit();
	} else {
		if (strlen($content) > 250) {
			echo('内容长度超过250个字符！');
			exit();	
		}
	}
	
	$data = array(
		'root_id' => $rid,
		'web_id' => $wid,
		'com_nick' => $nick,
		'com_email' => $email,
		'com_text' => $content,
		'com_ip' => $ipaddr,
		'com_status' => 1,
		'com_time' => $time,
	);
	$DB->insert($DB->table('comments'), $data);
	$insert_id = $DB->insert_id();
	if ($insert_id) {
		echo(1);
		exit();
	}
}

# score
if ($type == 'score') {
	$score = intval($_POST['score']);
	$wid = intval($_POST['wid']);
	
	$time = time();
	$cookieKey = "score-".$wid;
	$cookieValue = $_COOKIE[$cookieKey];
	
	if ($cookieValue != $cookieKey) {
		$DB->query("UPDATE ".$DB->table('webdata')." SET web_voter=web_voter+1, web_score=web_score+'$score' WHERE web_id='".$wid."'");
		$row = $DB->fetch_one("SELECT web_voter, web_score FROM ".$DB->table('webdata')." WHERE web_id='".$wid."' LIMIT 1");
		if ($row['web_voter'] > 0 && $row['web_score'] > 0) {
			$aver = $row['web_score'] / $row['web_voter'];
			$aver = round($aver, 1);
		} else {
			$aver = 0;
		}
		#设置COOKIE
		setcookie($cookieKey, $cookieKey, $time + 3600);
		echo $aver;
	} else {
		echo 1;
	}
}
?>