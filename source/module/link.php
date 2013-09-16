<?php
/** link list */
function get_link_list($where = 1, $field = 'link_id', $order = 'DESC', $start = 0, $pagesize = 0) {
	global $DB;
	
	$sql = "SELECT link_id, link_name, link_url, link_logo, link_hide, link_order FROM ".$DB->table('links')." WHERE $where ORDER BY $field $order LIMIT $start, $pagesize";
	$results = $DB->fetch_all($sql);
	
	return $results;
}
	
/** one link */
function get_one_link($link_id) {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('links')." WHERE link_id=$link_id LIMIT 1";
	$row = $DB->fetch_one($sql);
	
	return $row;
}

/** links */
function get_links() {
	global $DB;
	
	$links = load_cache('links') ? load_cache('links') : $DB->fetch_all("SELECT link_id, link_name, link_url, link_logo FROM ".$DB->table('links')." WHERE link_hide=1 ORDER BY link_order DESC, link_id ASC");
	
	return $links;
}
?>