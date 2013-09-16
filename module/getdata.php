<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

require(APP_PATH.'module/webdata.php');

$type = trim($_GET['type']);
$web_id = intval($_GET['wid']);

if (in_array($type, array('ip', 'grank', 'brank', 'srank', 'arank', 'clink', 'outstat', 'addfav', 'rate', 'error'))) {
	$where = "w.web_id=$web_id";
	$web = get_one_website($where);
	if (!$web) {
		exit();
	}
	
	$update_cycle = time() + (3600 * 24 * $options['data_update_cycle']);
	$update_time = time();
	if ($web['web_utime'] < $update_cycle) {
		$DB->query("UPDATE ".$DB->table('webdata')." SET web_utime='$update_time' WHERE web_id=".$web['web_id']);
		#server ip
		if ($type == 'ip') {
			$ip = get_serverip($web['web_url']);
			$ip = sprintf("%u", ip2long($ip));
			$DB->query("UPDATE ".$DB->table('webdata')." SET web_ip='$ip' WHERE web_id=".$web['web_id']);
		}
	
		#google pagerank
		if ($type == 'grank') {
			 $rank = get_pagerank($web['web_url']);
			 $DB->query("UPDATE ".$DB->table('webdata')." SET web_grank='$rank' WHERE web_id=".$web['web_id']);
		}
		
		#baidu pagerank
		if ($type == 'brank') {
			$rank = get_baidurank($web['web_url']);
			$DB->query("UPDATE ".$DB->table('webdata')." SET web_brank='$rank' WHERE web_id=".$web['web_id']);
		}
		
		#sogou pagerank
		if ($type == 'srank') {
			$rank = get_sogourank($web['web_url']);
			$DB->query("UPDATE ".$DB->table('webdata')." SET web_srank='$rank' WHERE web_id=".$web['web_id']);
		}
		
		#alexa rank
		if ($type == 'arank') {
			$rank = get_alexarank($web['web_url']);
			$DB->query("UPDATE ".$DB->table('webdata')." SET web_arank='$rank' WHERE web_id=".$web['web_id']);
		}
		
		#check link
		if ($type == 'clink') {
			if ($web['web_ispay'] == 0) {
				$content = get_url_content($web['web_url']);
				if (!empty($content)) {
					if (!preg_match('/<a(.*?)href=([\'\"]?)http:\/\/'.$options['check_link_name'].'([\/]?)([\'\"]?)(.*?)>'.$options['check_link_url'].'<\/a>/i', $content)) {
						$DB->query("UPDATE ".$DB->table('websites')." SET web_islink=1 WHERE web_id=".$web['web_id']);
					} else {
						$DB->query("UPDATE ".$DB->table('websites')." SET web_islink=0 WHERE web_id=".$web['web_id']);
					}
				}
			}
		}
	}
	
	#outstat
	if ($type == 'outstat') {
		$DB->query("UPDATE ".$DB->table('webdata')." SET web_outstat=web_outstat+1, web_otime=".time()." WHERE web_id=".$web['web_id']);
	}
	
	#favorite
	if ($type == 'addfav') {
		/** check login  */
		$auth_cookie = $_COOKIE['auth_cookie'];
		$myself = check_user_login($auth_cookie);
		if (empty($myself)) {
			exit('<script type="text/javascript">alert("您还未登录或未注册！"); window.location.href = "'.$options['site_root'].'member/?mod=login";</script>');
		}
		
		$query = $DB->query("SELECT web_id FROM ".$DB->table('favorites')." WHERE user_id=$myself[user_id] AND web_id=$web_id");
    	if ($DB->num_rows($query)) {
        	exit('<script type="text/javascript">alert("您已收藏过此网站！")</script>');
    	}
		$DB->free_result($query);
		
		/** insert */
		$DB->insert($DB->table('favorites'), array('user_id' => $myself['user_id'], 'web_id' => $web_id, 'fav_time' => time()));
		/** count */
		$count = $DB->get_count($DB->table('favorites'), array('web_id' => $web_id));
		$DB->update($DB->table('webdata'), array('web_fnum' => $count));
		exit('<script type="text/javascript">alert("网站收藏成功！")</script>');
	}
	
	#rate
	if ($type == 'rate') {
	/*	$do = trim($_GET['do']);
		if ($do == 'init') {
			$sql = "SELECT rate_score, rate_count FROM ".$DB->table('webdata')." WHERE web_id=".$web['web_id'];
			$row = $DB->fetch_one($sql);
			if ($row) {
				$score = @round($row['rate_score'] / $row['rate_count'], 2);
				echo "{\"rate_count\":\"".$row["rate_count"]."\", \"rate_score\":\"".$rate_score."\"}";
				//echo $score;
			}
		}
		
		if ($do == 'rate') {
			$num = intval($_GET['value']);
			if ($_COOKIE["rate_wid_".$web['web_id']] <> 1) {
				setcookie("rate_wid_".$web['web_id'], 1, time() + 3600);
				$DB->query("UPDATE ".$DB->table('webdata')." SET rate_score=rate_score+$num, rate_count=rate_count+1 WHERE web_id=".$web['web_id']);
				
				$sql = "SELECT rate_score, rate_count FROM ".$DB->table('webdata')." WHERE web_id=".$web['web_id'];
				$res = $DB->fetch_one($sql);
				$score = @round($res['rate_score'] / $res['rate_count'], 2);
				echo $score;
			}
		}
		*/
	}
	
	#error
	if ($type == 'error') {
		$DB->query("UPDATE ".$DB->table('webdata')." SET web_errors=web_errors+1, web_utime=".time()." WHERE web_id=".$web['web_id']);
	}
}
?>