<?php
/** page list */
function get_page_list($where = 1, $field = 'page_id', $order = 'DESC', $start = 0, $pagesize = 0) {
	global $DB;
	
	$sql = "SELECT page_id, page_name, page_intro, page_content FROM ".$DB->table('pages')." WHERE $where ORDER BY $field $order LIMIT $start, $pagesize";
	$results = $DB->fetch_all($sql);
	
	return $results;
}
	
/** one page */
function get_one_page($page_id) {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('pages')." WHERE page_id=$page_id LIMIT 1";
	$row = $DB->fetch_one($sql);
	
	return $row;
}

/** pages */
function get_pages() {
	global $DB;
	
	$sql = "SELECT page_id, page_name FROM ".$DB->table('pages')." ORDER BY page_id ASC";
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		$row['page_link'] = get_diypage_url($row['page_id']);
		$results[] = $row;
	}
	unset($row);
	$DB->free_result($query);
	
	return $results;
}
?>