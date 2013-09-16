<?php
/** config cache */
function options_cache() {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('options');
	$results = $DB->fetch_all($sql);
	$contents = "\$static_data = array(\r\n";
	foreach ($results as $row) {
		$contents .= "\t'".addslashes($row['option_name'])."' => '".addslashes($row['option_value'])."',\r\n";
	}
	$contents .= ");";
	unset($results);
	
	write_cache('options', $contents);
}

/** adver cache */
function advers_cache() {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('advers')." ORDER BY adver_id DESC";
	$results = $DB->fetch_all($sql);
	$contents = "\$static_data = array(\r\n";
	foreach ($results as $row) {
		$contents .= "\t'".$row['adver_id']."' => array(\r\n\t\t'adver_type' => '".$row['adver_type']."',\r\n\t\t'adver_name' => '".addslashes($row['adver_name'])."',\r\n\t\t'adver_url' => '".addslashes($row['adver_url'])."',\r\n\t\t'adver_code' => '".addslashes($row['adver_code'])."',\r\n\t\t'adver_etips' => '".$row['adver_etips']."',\r\n\t\t'adver_days' => '".$row['adver_days']."',\r\n\t\t'adver_date' => '".$row['adver_date']."'\r\n\t),\r\n";
	}
	$contents .= ");";
	unset($results);
	
	write_cache('advers', $contents);
}

/** link cache */
function links_cache() {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('links')." WHERE link_hide=1 ORDER BY link_order ASC";
	$results = $DB->fetch_all($sql);
	$contents = "\$static_data = array(\r\n";
	foreach ($results as $row) {
		$contents .= "\t'".$row['link_id']."' => array(\r\n\t\t'link_name' => '".addslashes($row['link_name'])."',\r\n\t\t'link_url' => '".addslashes($row['link_url'])."',\r\n\t\t'logo_url' => '".addslashes($row['link_logo'])."',\r\n\t),\r\n";
	}
	$contents .= ");";
	unset($results);
	
	write_cache('links', $contents);
}

/** category cache */
function categories_cache() {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('categories')." ORDER BY cate_order ASC, cate_id ASC";
	$results = $DB->fetch_all($sql);
	$contents .= "\$static_data = array(\r\n";
	foreach ($results as $row) {
		$contents .= "\t'".$row['cate_id']."' => array(\r\n\t\t'cate_id' => '".$row['cate_id']."',\r\n\t\t'root_id' => '".$row['root_id']."',\r\n\t\t'cate_mod' => '".$row['cate_mod']."',\r\n\t\t'cate_name' => '".addslashes($row['cate_name'])."',\r\n\t\t'cate_dir' => '".addslashes($row['cate_dir'])."',\r\n\t\t'cate_url' => '".addslashes($row['cate_url'])."',\r\n\t\t'cate_isbest' => '".$row['cate_isbest']."',\r\n\t\t'cate_keywords' => '".addslashes($row['cate_keywords'])."',\r\n\t\t'cate_description' => '".addslashes($row['cate_description'])."',\r\n\t\t'cate_arrparentid' => '".$row['cate_arrparentid']."',\r\n\t\t'cate_arrchildid' => '".$row['cate_arrchildid']."',\r\n\t\t'cate_childcount' => '".$row['cate_childcount']."',\r\n\t\t'cate_postcount' => '".$row['cate_postcount']."'\r\n\t),\r\n";
		
		$contents_1 = "\$static_data = array(\r\n";
		$contents_1 .= "\t'cate_id' => '".$row['cate_id']."',\r\n\t'root_id' => '".$row['root_id']."',\r\n\t'cate_mod' => '".$row['cate_mod']."',\r\n\t'cate_name' => '".addslashes($row['cate_name'])."',\r\n\t'cate_dir' => '".addslashes($row['cate_dir'])."',\r\n\t'cate_url' => '".addslashes($row['cate_url'])."',\r\n\t'cate_isbest' => '".$row['cate_isbest']."',\r\n\t'cate_keywords' => '".addslashes($row['cate_keywords'])."',\r\n\t'cate_description' => '".addslashes($row['cate_description'])."',\r\n\t'cate_arrparentid' => '".$row['cate_arrparentid']."',\r\n\t'cate_arrchildid' => '".$row['cate_arrchildid']."',\r\n\t'cate_childcount' => '".$row['cate_childcount']."',\r\n\t'cate_postcount' => '".$row['cate_postcount']."',\r\n";
		$contents_1 .= ");";
		
		write_cache('category_'.$row['cate_id'], $contents_1);
	}
	$contents .= ");";
	unset($results);
	
	write_cache('categories', $contents);
}

/** label cache */
function labels_cache() {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('labels');
	$results = $DB->fetch_all($sql);
	$contents = "\$static_data = array(\r\n";
	foreach ($results as $row) {
		$contents .= "\t'".addslashes($row['label_name'])."' => '".addslashes($row['label_content'])."',\r\n";
	}
	$contents .= ");";
	unset($results);
	
	write_cache('labels', $contents);
}

/** archives cache */
function archives_cache() {
	global $DB;
	
	$sql = "SELECT web_ctime FROM ".$DB->table('websites')." WHERE web_status=3 ORDER BY web_ctime DESC";
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		$results[] = date('Y-m', $row['web_ctime']);
	}
	unset($row);
	$DB->free_result($query);
		
	$count = array_count_values($results);
	unset($results);
	
	foreach ($count as $key => $val) {
		list($year, $month) = explode('-', $key);
		$archives[$year][$month] = $val;
	}
		
	$contents = "\$static_data = array(\r\n";	
	foreach ($archives as $year => $arr) {
		$contents .= "\t'".$year."' => array(";
		ksort($arr);
		foreach ($arr as $month => $num) {
			$contents .= "\r\n\t\t'".$month."' => '".$num."',";
		}
		$contents .= "\r\n\t),\r\n";
	}
	$contents .= ");";
	unset($archives);
	
	write_cache('archives', $contents);
}

/** stats cache */
function stats_cache() {
	global $DB;
	
	$category = $DB->get_count($DB->table('categories'));
	$website = $DB->get_count($DB->table('websites'));
	$article = $DB->get_count($DB->table('articles'));
	$audit = $DB->get_count($DB->table('websites'), array('web_status' => 2));
	$user = $DB->get_count($DB->table('users'), "user_type != 'admin'");
	$adver = $DB->get_count($DB->table('advers'));
	$link = $DB->get_count($DB->table('links'));
	$feedback = $DB->get_count($DB->table('feedbacks'));
	$label = $DB->get_count($DB->table('labels'));
	$page = $DB->get_count($DB->table('pages'));
	
	$contents = "\$static_data = array(\r\n";
	$contents .= "\t'category' => '".$category."',\r\n";
	$contents .= "\t'website' => '".$website."',\r\n";
	$contents .= "\t'article' => '".$article."',\r\n";
	$contents .= "\t'audit' => '".$audit."',\r\n";
	$contents .= "\t'user' => '".$user."',\r\n";
	$contents .= "\t'adver' => '".$adver."',\r\n";
	$contents .= "\t'link' => '".$link."',\r\n";
	$contents .= "\t'feedback' => '".$feedback."',\r\n";
	$contents .= "\t'label' => '".$label."',\r\n";
	$contents .= "\t'page' => '".$page."',\r\n";
	$contents .= ");";
	
	write_cache('stats', $contents);
}
?>