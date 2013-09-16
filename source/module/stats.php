<?php
function get_stats() {
	global $DB;
	
	$stat = array();
	$stat['category'] = $DB->get_count($DB->table('categories'));
	$stat['website'] = $DB->get_count($DB->table('websites'));
	$stat['article'] = $DB->get_count($DB->table('articles'));
	$stat['apply'] = $DB->get_count($DB->table('websites'), array('web_status' => 2));
	$stat['user'] = $DB->get_count($DB->table('users'), 'user_type' != 'admin');
	$stat['adver'] = $DB->get_count($DB->table('advers'));
	$stat['link'] = $DB->get_count($DB->table('links'));
	$stat['feedback'] = $DB->get_count($DB->table('feedbacks'));
	$stat['label'] = $DB->get_count($DB->table('labels'));
	$stat['page'] = $DB->get_count($DB->table('pages'));
	
	return $stat;
}
?>