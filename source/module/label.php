<?php
/** label list */
function get_label_list($where = 1, $field = 'label_id', $order = 'DESC', $start = 0, $pagesize = 0) {
	global $DB;
	
	$sql = "SELECT label_id, label_name, label_intro FROM ".$DB->table('labels')." WHERE $where ORDER BY $field $order LIMIT $start, $pagesize";
	$results = $DB->fetch_all($sql);
	
	return $results;
}
	
/** one label */
function get_one_label($label_id) {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('labels')." WHERE label_id=$label_id LIMIT 1";
	$row = $DB->fetch_one($sql);
	
	return $row;
}

/** labels */
function get_labels() {
	global $DB;
	
	$labels = load_cache('labels') ? load_cache('labels') : $DB->fetch_all("SELECT label_name, label_content FROM ".$DB->table('labels')." ORDER BY label_id ASC");
	
	return $labels;
}
?>