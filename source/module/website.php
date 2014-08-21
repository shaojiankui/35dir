<?php
/** website list */
function get_websites($cate_id = 0, $top_num = 10, $is_pay = false, $is_best = false, $field = 'ctime', $order = 'desc') {
	global $DB;
	
	$where = "w.web_status=3 AND c.cate_mod='webdir'";
	if (!in_array($field, array('instat', 'outstat', 'views', 'ctime'))) $field = 'ctime';
	if ($cate_id > 0) {
		$cate = get_one_category($cate_id);
		if (!empty($cate)) $where .= " AND w.cate_id IN (".$cate['cate_arrchildid'].")";
	}
	if ($is_pay == true) $where .= " AND w.web_ispay=1";
	if ($is_best == true) $where .= " AND w.web_isbest=1";
	switch ($field) {
		case 'instat' :
			$sortby = "d.web_itime";
			break;
		case 'outstat' :
			$sortby = "d.web_otime";
			break;
		case 'views' :
			$sortby = "d.web_views";
			break;
		case 'ctime' :
			$sortby = "w.web_ctime";
			break;
		default :
			$sortby = "w.web_ctime";
			break;
	}
	$order = strtoupper($order);
	
	$sql = "SELECT w.web_id, w.web_name, w.web_url, w.web_pic, w.web_intro, w.web_ctime, c.cate_id, c.cate_mod, c.cate_name, d.web_grank, d.web_brank, d.web_srank, d.web_arank, d.web_instat, d.web_outstat, d.web_views FROM ".$DB->table('websites')." w LEFT JOIN ".$DB->table('categories')." c ON w.cate_id=c.cate_id LEFT JOIN ".$DB->table('webdata')." d ON w.web_id=d.web_id WHERE $where ORDER BY $sortby $order LIMIT $top_num";
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		$row['web_furl'] = format_url($row['web_url']);
		//有缩略图显示本地 没有缩略图调用爱站修改方法
		$row['web_pic'] = get_webthumb($row['web_pic'],$row['web_url']);
		//end by sjk
		$row['web_tags'] = get_format_tags($row['web_tags']);
		$row['web_ctime'] = date('Y-m-d', $row['web_ctime']);
		$row['web_link'] = get_website_url($row['web_id']);
		$row['cate_link'] = get_category_url($row['cate_mod'], $row['cate_id']);
		$results[] = $row;
	}
	unset($row);
	$DB->free_result($query);
	
	return $results;
}

/** website list */
function get_website_list($where = 1, $field = 'ctime', $order = 'DESC', $start = 0, $pagesize = 0) {
	global $DB;
	
	if (!in_array($field, array('instat', 'outstat', 'views', 'ctime'))) $field = 'ctime';
	switch ($field) {
		case 'instat' :
			$sortby = "d.web_instat";
			break;
		case 'outstat' :
			$sortby = "d.web_outstat";
			break;
		case 'views' :
			$sortby = "d.web_views";
			break;
		case 'ctime' :
			$sortby = "w.web_ctime";
			break;
		default :
			$sortby = "w.web_ctime";
			break;
	}
	$order = strtoupper($order);
	$sql = "SELECT w.web_id, w.web_name, w.web_url, w.web_pic, w.web_intro, w.web_ispay, w.web_istop, w.web_isbest, w.web_status, w.web_ctime, c.cate_name, d.web_ip, d.web_grank, d.web_brank, d.web_srank, d.web_arank, d.web_instat, d.web_outstat, d.web_views, d.web_utime FROM ".$DB->table('websites')." w LEFT JOIN ".$DB->table('categories')." c ON w.cate_id=c.cate_id LEFT JOIN ".$DB->table('webdata')." d ON w.web_id=d.web_id WHERE $where ORDER BY w.web_istop DESC, $sortby $order LIMIT $start, $pagesize";
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		switch ($row['web_status']) {
			case 1 :
				$status = '黑名单';
				break;
			case 2 :
				$status = '待审核';
				break;
			case 3 :
				$status = '已审核';
				break;
		}
		$row['web_furl'] = format_url($row['web_url']);
		$row['web_link'] = get_website_url($row['web_id']);
		//有缩略图显示本地 没有缩略图调用爱站修改方法
		$row['web_pic'] = get_webthumb($row['web_pic'],$row['web_url']);
		//end by sjk
		$row['web_status'] = $status;
		$row['web_ctime'] = date('Y-m-d', $row['web_ctime']);
		$row['web_utime'] = date('Y-m-d', $row['web_utime']);
		$results[] = $row;
	}
	unset($row);
	$DB->free_result($query);
		
	return $results;
}
	
/** one website */
function get_one_website($where = 1) {
	global $DB;
	
	$row = $DB->fetch_one("SELECT w.user_id, w.cate_id, w.web_id, w.web_name, w.web_url, w.web_tags, w.web_pic, w.web_intro, w.web_ispay, w.web_istop, w.web_isbest, w.web_status, w.web_ctime, d.web_ip, d.web_grank, d.web_brank, d.web_srank, d.web_arank, d.web_instat, d.web_outstat, d.web_views, d.web_voter, d.web_score, d.web_utime FROM ".$DB->table('websites')." w LEFT JOIN ".$DB->table("webdata")." d ON w.web_id=d.web_id WHERE $where LIMIT 1");
	
	return $row;
}
	
/** rssfeed */
function get_website_rssfeed($cate_id = 0) {
	global $DB, $options;
		
	$where = "w.web_status=3 AND c.cate_mod='webdir'";
	$cate = get_one_category($cate_id);
	if (!empty($cate)) {
		if ($cate['cate_childcount'] > 0) {
			$where .= " AND w.cate_id IN (".$cate['cate_arrchildid'].")";
		} else {
			$where .= " AND w.cate_id=$cate_id";
		}
	}

	$sql = "SELECT w.web_id, w.cate_id, w.web_name, w.web_url, w.web_intro, w.web_ctime, c.cate_name FROM ".$DB->table('websites')." w LEFT JOIN ".$DB->table('categories')." c ON w.cate_id=c.cate_id";
	$sql .= " WHERE $where ORDER BY w.web_id DESC LIMIT 50";
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		$row['web_intro'] = htmlspecialchars(strip_tags($row['web_intro']));
		$row['web_ctime'] = date('Y-m-d H:i:s', $row['web_ctime']);
		$row['web_link'] = str_replace('&', '&amp;', get_website_url($row['web_id'], true));
		$results[] = $row;
	}
	unset($row);
	$DB->free_result($query);
		
	header("Content-Type: application/xml;");
	echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
	echo "<rss version=\"2.0\">\n";
	echo "<channel>\n";
	echo "<title>".$options['site_name']."</title>\n";
	echo "<link>".$options['site_url']."</link>\n";
	echo "<description>".$options['site_description']."</description>\n";
	echo "<language>zh-cn</language>\n";
	echo "<copyright><!--CDATA[".$options['site_copyright']."]--></copyright>\n";
	echo "<webmaster>".$options['site_name']."</webmaster>\n";
	echo "<generator>".$options['site_name']."</generator>\n";
	echo "<image>\n";
	echo "<title>".$options['site_name']."</title>\n";
	echo "<url>".$options['site_url']."logo.gif</url>\n";
	echo "<link>".$options['site_url']."</link>\n";
	echo "<description>".$options['site_description']."</description>\n";
	echo "</image>\n";
	
	foreach ($results as $row) {
		echo "<item>\n";
		echo "<link>".$row['web_link']."</link>\n";
		echo "<title>".$row['web_name']."</title>\n";
		echo "<author>".$options['site_name']."</author>\n";
		echo "<category>".$row['cate_name']."</category>\n";
		echo "<pubDate>".$row['web_ctime']."</pubDate>\n";
		echo "<guid>".$row['web_link']."</guid>\n";
		echo "<description>".$row['web_intro']."</description>\n";
		echo "</item>\n";
	}
	echo "</channel>\n";
	echo "</rss>";
	
	unset($options, $results);
}
	
/** sitemap */
function get_website_sitemap($cate_id = 0) {
	global $DB, $options;
	
	$where = "web_status=3";
	$cate = get_one_category($cate_id);
	if (!empty($cate)) {
		if ($cate['cate_childcount'] > 0) {
			$where .= " AND cate_id IN (".$cate['cate_arrchildid'].")";
		} else {
			$where .= " AND cate_id=$cate_id";
		}
	}

	$sql = "SELECT web_id, web_url, web_ctime FROM ".$DB->table('websites');
	$sql .= " WHERE $where ORDER BY web_id DESC LIMIT 5000"; //修改sitemap显示条数为5000
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		$row['web_ctime'] = date('Y-m-d H:i:s', $row['web_ctime']);
		$row['web_link'] = str_replace('&', '&amp;', get_website_url($row['web_id'], true));
		$results[] = $row;
	}
	unset($row);
	$DB->free_result($query);
	
	header("Content-Type: application/xml;");
	echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
	echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
	echo "<url>\n";
	echo "<loc>".$options['site_url']."</loc>\n";
	echo "<lastmod>".iso8601('Y-m-d\TH:i:s\Z')."</lastmod>\n";
	echo "<changefreq>always</changefreq>\n";
	echo "<priority>0.9</priority>\n";
	echo "</url>\n";
	
	$now = time();
	foreach ($results as $row) {
		$prior = 0.5;
		
		if (datediff('h', $row['web_ctime']) < 24) {
			$freq = "hourly";
			$prior = 0.8;
		} elseif (datediff('d', $row['web_ctime']) < 7) {
			$freq = "daily";
			$prior = 0.7;
		} elseif (datediff('w', $row['web_ctime']) < 4) {
			$freq = "weekly";
		} elseif (datediff('m', $row['web_ctime']) < 12) {
			$freq = "monthly";
		} else {
			$freq = "yearly";
		}
		
		echo "<url>\n";
		echo "<loc>".$row['web_link']."</loc>\n";
		echo "<lastmod>".iso8601('Y-m-d\TH:i:s\Z', $row['web_ctime'])."</lastmod>\n";
		echo "<changefreq>".$freq."</changefreq>\n";
		if ($prior != 0.5) {
			echo "<priority>".$prior."</priority>\n";
		}
		echo "</url>\n";
	}
	echo "</urlset>";
	
	unset($options, $results);
}

/** sodir api */
function get_website_api($cate_id = 0, $start = 0, $pagesize = 0) {
	global $DB, $options;
		
	$where = "w.web_status=3 AND c.cate_mod='webdir'";
	$cate = get_one_category($cate_id);
	if (!empty($cate)) {
		if ($cate['cate_childcount'] > 0) {
			$where .= " AND w.cate_id IN (".$cate['cate_arrchildid'].")";
		} else {
			$where .= " AND w.cate_id=$cate_id";
		}
	}

	$sql = "SELECT w.web_id, w.cate_id, w.web_name, w.web_url, w.web_tags, w.web_intro, w.web_ctime, c.cate_name FROM ".$DB->table('websites')." w LEFT JOIN ".$DB->table('categories')." c ON w.cate_id=c.cate_id";
	$sql .= " WHERE $where ORDER BY w.web_id DESC LIMIT $start, $pagesize";
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		$row['web_intro'] = htmlspecialchars(strip_tags($row['web_intro']));
		$row['web_ctime'] = date('Y-m-d H:i:s', $row['web_ctime']);
		$row['web_link'] = str_replace('&', '&amp;', get_website_url($row['web_id'], true));
		$results[] = $row;
	}
	unset($row);
	$DB->free_result($query);
	
	$total = $DB->get_count($DB->table('websites').' w', "");
	
	header("Content-Type: application/xml;");
	echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
	echo "<urlset xmlns=\"http://www.sodir.org/sitemap/\">\n";
	echo "<total>".$total."</total>";
	
	foreach ($results as $row) {
		echo "<url>\n";
		echo "<name>".$row['web_name']."</name>\n";
		echo "<link>".$row['web_link']."</link>\n";
		echo "<tags>".$row['web_tags']."</tags>\n";
		echo "<desc>".$row['web_intro']."</desc>\n";
		echo "<cate>".$row['cate_name']."</cate>\n";
		echo "<time>".$row['web_ctime']."</time>\n";		
		echo "</url>\n";
	}
	echo "</urlset>\n";
	
	unset($options, $results);
}

/** archives */
function get_archives() {
	global $DB;
	
	$archives = array();
	if (load_cache('archives')) {
		$archives = load_cache('archives');
	} else {
		$time = array();
		$sql = "SELECT web_ctime FROM ".$DB->table('websites')." WHERE web_status=3 ORDER BY web_ctime DESC";
		$query = $DB->query($sql);
		while ($row = $DB->fetch_array($query)) {
			$time[] = date('Y-m', $row['web_ctime']);
		}
		unset($row);
		$DB->free_result($query);
		
		$count = array_count_values($time);
		unset($time);
		
		foreach ($count as $key => $val) {
			list($year, $month) = explode('-', $key);
			$archives[$year][$month] = $val;
		}
	}
		
	$newarr = array();
	foreach ($archives as $year => $arr) {
		foreach ($arr as $month => $count) {
			$newarr[$year][$month]['site_count'] = $count;
			$newarr[$year][$month]['arc_link'] = get_archives_url($year.$month);
		}
	}
	unset($archives);
	
	return $newarr;
}
?>