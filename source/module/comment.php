<?php
/** comment list */
function get_comment_list($where = 1, $start = 0, $pagesize = 0) {
	global $DB;
	
	$sql = "SELECT com_id, web_id, root_id, com_nick, com_email, com_text, com_ip, com_time FROM ".$DB->table('comments')." WHERE $where ORDER BY com_id DESC LIMIT $start, $pagesize";
	$query = $DB->query($sql);
	$comments = array();
	while ($row = $DB->fetch_array($query)) {
		switch ($row['com_status']) {
			case 1 :
				$status = '待审核';
				break;
			case 2 :
				$status = '已审核';
				break;
		}
		$count = get_comment_count($row['com_id'], $row['web_id']);
		if ($count > 0) {
			$row['reply_comments'] = get_comments($row['com_id'], $row['web_id']);
		}
		$row['com_ip'] = long2ip($row['com_ip']);
		$row['com_time'] = get_format_time($row['com_time']);
		$comments[] = $row;
	}
	$DB->free_result($query);
	
	return $comments;
}

/** comments list */
function get_comments($com_id = 0, $web_id = 0, $top_num = 10) {
	global $DB;
	
	$sql = "SELECT com_id, web_id, root_id, com_nick, com_email, com_text, com_ip, com_time FROM ".$DB->table('comments')." WHERE com_status=1";
	if ($com_id > 0) $sql .= " AND root_id = '$com_id'";
	if ($web_id > 0) $sql .= " AND web_id = '$web_id'";
	$sql .= " ORDER BY com_id DESC";
	if ($top_num > 0) $sql .= " LIMIT $top_num";
	$query = $DB->query($sql);
	$comments = array();
	while ($row = $DB->fetch_array($query)) {
		$count = get_comment_count($row['web_id'], $row['com_id']);
		if ($count > 0) {
			$row['reply_comments'] = get_comments($row['web_id'], $row['com_id']);
		}
		$row['com_ip'] = long2ip($row['com_ip']);
		$row['com_time'] = get_format_time($row['com_time']);
		$comments[] = $row;
	}
	$DB->free_result($query);
	
	return $comments;
}

/** comments count */
function get_comment_count($com_id = 0, $web_id = 0) {
	global $DB;
	
	$where = "root_id='$com_id' AND web_id='$web_id'";
	$count = $DB->get_count($DB->table('comments'), $where);
	
	return $count;
}
?>