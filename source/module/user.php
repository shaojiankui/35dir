<?php
/** check login */
function check_user_login($data) {
	global $DB, $user_types;
	
	list($user_id, $user_pass, $login_count) = $data ? explode('|', authcode($data, 'DECODE')) : array('', '', '');
	$user_id = intval($user_id);
	$user_pass = addslashes($user_pass);
	$userinfo = array();
	if ($user_id && $user_pass) {
		$row = $DB->fetch_one("SELECT user_id, user_type, user_email, user_pass, nick_name, user_qq, user_status, join_time, login_time, login_ip, login_count FROM ".$DB->table('users')." WHERE user_id=$user_id");
		if ($row['user_pass'] == $user_pass && $row['login_count'] == $login_count) {
			$userinfo = array(
				'user_id' => $row['user_id'],
				'user_type' => $user_types[$row['user_type']],
				'user_email' => $row['user_email'],
				'nick_name' => $row['nick_name'],
				'user_qq' => $row['user_qq'],
				'user_status' => $row['user_status'],
				'join_time' => date('Y-m-d H:i:s', $row['join_time']),
				'login_time' => date('Y-m-d H:i:s', $row['login_time']),
				'login_ip' => long2ip($row['login_ip']),
				'login_count' => $row['login_count'],
			);
		}
	}
	
	return $userinfo;
}

/** user list */
function get_user_list($where = 1, $field = 'join_time', $order = 'DESC', $start = 0, $pagesize = 0) {
	global $DB;
	
	$sql = "SELECT user_id, user_type, user_email, nick_name, user_qq, join_time, user_status FROM ".$DB->table('users')." WHERE $where ORDER BY $field $order LIMIT $start, $pagesize";
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		$results[] = $row;
	}
	unset($row);
	$DB->free_result($query);
		
	return $results;
}
	
/** one user */
function get_one_user($user_id) {
	global $DB;
	
	$row = $DB->fetch_one("SELECT user_id, user_type, user_email, nick_name, user_qq, join_time, user_status FROM ".$DB->table('users')." WHERE user_id=$user_id LIMIT 1");
	
	return $row;
}

/** user option */
function get_usertype_option($type = 'member') {
	global $user_types;
	
	foreach ($user_types as $key => $val) {
		$optstr .= '<option value="'.$key.'"';
		if ($type == $key) $optstr .= ' selected';
		$optstr .= '>'.$val.'</option>';
	}
	
	return $optstr;
}
?>