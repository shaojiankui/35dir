<?php
/** favorite list */
function get_favorites($cate_id = 0, $top_num = 10, $field = 'ftime', $order = 'desc') {

}

/** favorite list */
function get_favorite_list($where = 1, $field = 'ftime', $order = 'DESC', $start = 0, $pagesize = 0) {
	global $DB;
	
	if (!in_array($field, array('fnum', 'ftime'))) $field = 'ftime';
	switch ($field) {
		case 'fnum' :
			$sortby = "f.fav_num";
			break;
		case 'ftime' :
			$sortby = "f.fav_time";
			break;
		default :
			$sortby = "f.fav_time";
			break;
	}
	$order = strtoupper($order);
	$sql = "SELECT f.fav_id, f.fav_time, w.web_name, w.web_url FROM ".$DB->table('favorites')." f LEFT JOIN ".$DB->table('websites')." w ON f.web_id=w.web_id WHERE $where ORDER BY $sortby $order LIMIT $start, $pagesize";
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		$row['fav_time'] = date('Y-m-d', $row['fav_time']);
		$results[] = $row;
	}
	unset($row);
	$DB->free_result($query);
		
	return $results;
}
?>