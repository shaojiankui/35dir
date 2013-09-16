<?php
/** weblink list */
function get_weblinks($deal_type = 0, $top_num = 10, $field = 'time', $order = 'desc') {
	global $DB, $deal_types;

	$where = "l.link_hide=0";
	if ($deal_type > 0) $where .= "AND l.deal_type=$deal_type";
	
	if (!in_array($field, array('id', 'time'))) $field = 'time';
	switch ($field) {
		case 'time' :
			$sortby = "l.link_time";
			break;
		default :
			$sortby = "l.link_id";
			break;
	}
	$order = strtoupper($order);
	
	$sql = "SELECT l.link_id, l.user_id, l.deal_type, l.link_name, l.link_price, l.link_days, l.link_time, w.web_name, w.web_url, c.cate_id, c.cate_name, d.web_grank, d.web_brank, d.web_srank, d.web_arank FROM ".$DB->table('weblinks')." l LEFT JOIN ".$DB->table('websites')." w ON l.web_id=w.web_id LEFT JOIN ".$DB->table('categories')." c ON w.cate_id=c.cate_id LEFT JOIN ".$DB->table('webdata')." d ON w.web_id=d.web_id WHERE $where ORDER BY l.link_istop DESC, $sortby $order LIMIT $top_num";
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		$row['deal_type'] = $deal_types[$row['deal_type']];
		$row['link_price'] = ($row['link_price'] > 0 ? $row['link_price'] : '面议');
		$row['link_time'] = date('Y-m-d', $row['link_time']);
		$row['web_link'] = get_weblink_url($row['link_id']);
		$results[] = $row;
	}
	unset($row);
	$DB->free_result($query);
	
	return $results;
}

/** weblink list */
function get_weblink_list($where = 1, $field = 'time', $order = 'DESC', $start = 0, $pagesize = 0) {
	global $DB, $deal_types;
	
	if (!in_array($field, array('id', 'time'))) $field = 'time';
	switch ($field) {
		case 'time' :
			$sortby = "l.link_time";
			break;
		default :
			$sortby = "l.link_id";
			break;
	}
	$order = strtoupper($order);
	$sql = "SELECT l.link_id, l.user_id, l.deal_type, l.link_name, l.link_price, l.link_days, l.link_time, w.web_name, w.web_url, c.cate_id, c.cate_name, d.web_grank, d.web_brank, d.web_srank, d.web_arank FROM ".$DB->table('weblinks')." l LEFT JOIN ".$DB->table('websites')." w ON l.web_id=w.web_id LEFT JOIN ".$DB->table('categories')." c ON w.cate_id=c.cate_id LEFT JOIN ".$DB->table('webdata')." d ON w.web_id=d.web_id WHERE $where ORDER BY l.link_istop DESC, $sortby $order LIMIT $start, $pagesize";
	$results = $DB->fetch_all($sql);
		
	return $results;
}
	
/** one weblink */
function get_one_weblink($where = 1) {
	global $DB;
	
	$row = $DB->fetch_one("SELECT l.link_id, l.user_id, l.web_id, l.deal_type, l.link_name, l.link_type, l.link_pos, l.link_price, l.link_if1, l.link_if2, l.link_if3, l.link_if4, l.link_intro, l.link_days, l.link_views, l.link_time, w.web_id, w.cate_id, w.web_name, w.web_url, w.web_pic FROM ".$DB->table('weblinks')." l LEFT JOIN ".$DB->table("websites")." w ON l.web_id=w.web_id WHERE $where LIMIT 1");
	
	return $row;
}

/** weburl option */
function get_weburl_option($user_id = 0, $web_id = 0) {
	global $DB;
	
	$sql = "SELECT web_id, web_name, web_url FROM ".$DB->table('websites')." WHERE web_status=3 AND user_id=$user_id ORDER BY web_id DESC";
	$results = $DB->fetch_all($sql);
	$optstr = '';
	foreach ($results as $row) {
		$optstr .= '<option value="'.$row['web_id'].'"';
		if ($web_id > 0 && $web_id == $row['web_id']) $optstr .= ' selected';
		$optstr .= '>'.$row['web_name'].' - '.$row['web_url'].'</option>';
	}
	unset($results);
		
	return $optstr;
}

/** dealtype radio */
function get_dealtype_radio($type = 1) {
	global $deal_types;
	
	$optstr = '';
	foreach ($deal_types as $key => $val) {
		$optstr .= '<input type="radio" name="deal_type" id="deal_type_'.$key.'" value="'.$key.'"';
		if ($type == $key) $optstr .= ' checked';
		$optstr .= '><label for="deal_type_'.$key.'">'.$val.'<label>　';
	}
	unset($deal_types);
		
	return $optstr;
}

/** linktype radio */
function get_linktype_radio($type = 1) {
	global $link_types;
	
	$optstr = '';
	foreach ($link_types as $key => $val) {
		$optstr .= '<input type="radio" name="link_type" id="link_type_'.$key.'" value="'.$key.'"';
		if ($type == $key) $optstr .= ' checked';
		$optstr .= '><label for="link_type_'.$key.'">'.$val.'<label>　';
	}
	unset($link_types);
		
	return $optstr;
}

/** linkpos radio */
function get_linkpos_radio($pos = 1) {
	global $link_pos;
	
	$optstr = '';
	foreach ($link_pos as $key => $val) {
		$optstr .= '<input type="radio" name="link_pos" id="link_pos_'.$key.'" value="'.$key.'"';
		if ($pos == $key) $optstr .= ' checked';
		$optstr .= '><label for="link_pos_'.$key.'">'.$val.'<label>　';
	}
	unset($link_pos);
		
	return $optstr;
}
?>