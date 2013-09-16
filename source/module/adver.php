<?php
/** adver list */
function get_adver_list($where = 1, $field = 'adver_id', $order = 'DESC', $start = 0, $pagesize = 0) {
	global $DB;
	
	$sql = "SELECT adver_id, adver_type, adver_name, adver_url, adver_code, adver_etips, adver_days, adver_date FROM ".$DB->table('advers')." WHERE $where ORDER BY $field $order LIMIT $start, $pagesize";
	$results = $DB->fetch_all($sql);
	
	return $results;
}
	
/** one adver */
function get_one_adver($adver_id) {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('advers')." WHERE adver_id=$adver_id LIMIT 1";
	$row = $DB->fetch_one($sql);
	
	return $row;
}

/** adtype option */
function get_adtype_option($adtype = 0) {
	$types = array('0' => '所有类型', '1' => '文字链接', '2' => '广告代码');
	
	$option = '';
	foreach ($types as $key => $val) {
		$option .= '<option value="'.$key.'"';
		if ($adtype == $key) {
			$option .= ' selected';
		}
		$option .= '>'.$val.'</option>';
	}
	
	return $option;
}

/** all advers*/
function get_all_adver() {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('advers')." ORDER BY adver_id ASC";
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		$results[$row['adver_id']] = $row;
	}
	unset($row);
	$DB->free_result($query);
		
	return $results;
}

/** type ads */
function get_adver($type = 1) {
	global $DB;
	
	$advers = load_cache('advers') ? load_cache('advers') : get_all_adver();
	$ads = array();
	foreach ($advers as $aid => $ad) {
		if ($ad['adver_type'] == $type) {
			$ads[$aid] = $ad;
		}
	}
	unset($advers);
	
	return $ads;
}

/** text ads */
function get_adlinks() {
	$ads = get_adver(1);
	if (!empty($ads)) {
		return $ads;
	}
}

/** code ads */
function get_adcode($aid = 0) {
	$ads = get_adver(2);
	if (is_array($ads[$aid])) {
		$ad_code = stripslashes($ads[$aid]['adver_code']);
		$ad_tips = $ads[$aid]['adver_etips'];
		$ad_days = $ads[$aid]['adver_days'];
		$ad_date = $ads[$aid]['adver_date'];
		
		$endtime = $ad_date + $ad_days * 24 * 3600;
		if ($ad_days > 0) {
			return $endtime > $adver['adver_date'] ? $ad_code : $ad_tips;
		} else {
			return $ad_code;
		}
	}
}
?>