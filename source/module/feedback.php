<?php
/** feedback list */
function get_feedback_list($where = 1, $field = 'fb_id', $order = 'DESC', $start = 0, $pagesize = 0) {
	global $DB;
	
	$sql = "SELECT fb_id, fb_nick, fb_email, fb_content, fb_date FROM ".$DB->table('feedbacks')." WHERE $where ORDER BY $field $order LIMIT $start, $pagesize";
	$results = $DB->fetch_all($sql);
	
	return $results;
}
	
/** one feedback */
function get_one_feedback($fb_id) {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('feedbacks')." WHERE fb_id=$fb_id LIMIT 1";
	$row = $DB->fetch_one($sql);
	
	return $row;
}
?>