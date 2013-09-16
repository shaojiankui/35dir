<?php
/** article list */
function get_articles($cate_id = 0, $top_num = 10, $is_best = false, $field = 'ctime', $order = 'desc') {
	global $DB;
	
	$where = "a.art_status=3 AND c.cate_mod='article'";
	if (!in_array($field, array('views', 'ctime'))) $field = 'ctime';
	if ($cate_id > 0) {
		$cate = get_one_category($cate_id);
		if (!empty($cate)) $where .= " AND a.cate_id IN ('".$cate['cate_arrchildid']."')";
	}
	if ($is_best == true) $where .= " AND a.art_isbest=1";
	switch ($field) {
		case 'views' :
			$sortby = "a.art_views";
			break;
		case 'ctime' :
			$sortby = "a.art_ctime";
			break;
		default :
			$sortby = "a.art_ctime";
			break;
	}
	$order = strtoupper($order);
	
	$sql = "SELECT a.art_id, a.art_title, a.art_tags, a.art_intro, a.art_views, a.art_ctime, c.cate_id, c.cate_mod, c.cate_name, c.cate_dir FROM ".$DB->table('articles')." a LEFT JOIN ".$DB->table('categories')." c ON a.cate_id=c.cate_id WHERE $where ORDER BY $sortby $order LIMIT $top_num";
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		$row['art_link'] = get_article_url($row['art_id']);
		$row['art_tags'] = get_format_tags($row['art_tags']);
		$row['art_ctime'] = date('Y-m-d', $row['art_ctime']);
		$row['art_utime'] = date('Y-m-d', $row['art_utime']);
		$row['cate_link'] = get_category_url($row['cate_mod'], $row['cate_id']);
		$results[] = $row;
	}
	unset($row);
	$DB->free_result($query);
	
	return $results;
}

/** article list */
function get_article_list($where = 1, $field = 'ctime', $order = 'DESC', $start = 0, $pagesize = 0) {
	global $DB;
	
	if (!in_array($field, array('views', 'ctime'))) $field = 'ctime';
	switch ($field) {
		case 'views' :
			$sortby = "a.art_views";
			break;
		case 'ctime' :
			$sortby = "a.art_ctime";
			break;
		default :
			$sortby = "a.art_ctime";
			break;
	}
	$order = strtoupper($order);
	$sql = "SELECT a.art_id, a.art_title, a.art_tags, a.art_intro, a.art_views, a.art_istop, a.art_isbest, a.art_status, a.art_ctime, c.cate_id, c.cate_name FROM ".$DB->table('articles')." a LEFT JOIN ".$DB->table('categories')." c ON a.cate_id=c.cate_id WHERE $where ORDER BY a.art_istop DESC, $sortby $order LIMIT $start, $pagesize";
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		switch ($row['art_status']) {
			case 1 :
				$art_status = '<font color="#333333">草稿</font>';
				break;
			case 2 :
				$art_status = '<font color="#ff3300">待审核</font>';
				break;
			case 3 :
				$art_status = '<font color="#008800">已审核</font>';
				break;
		}
		$art_istop = $row['art_istop'] > 0 ? '<font color="#ff0000">置顶</font>' : '<font color="#cccccc">置顶</font>';
		$art_isbest = $row['art_isbest'] > 0 ? '<font color="#ff3300">推荐</font>' : '<font color="#cccccc">推荐</font>';
		$row['art_attr'] = $art_istop.' - '.$art_isbest.' - '.$art_status;
		$row['art_link'] = get_article_url($row['art_id']);
		$row['art_ctime'] = date('Y-m-d', $row['art_ctime']);
		$row['art_utime'] = date('Y-m-d', $row['art_utime']);
		$results[] = $row;
	}
	unset($row);
	$DB->free_result($query);
		
	return $results;
}
	
/** one article */
function get_one_article($where = 1) {
	global $DB;
	
	$row = $DB->fetch_one("SELECT a.user_id, a.cate_id, a.art_id, a.art_title, a.art_tags, a.copy_from, a.copy_url, a.art_intro, a.art_content, a.art_views, a.art_istop, a.art_isbest, a.art_status, a.art_ctime, a.art_utime, c.cate_id, c.cate_name FROM ".$DB->table('articles')." a LEFT JOIN ".$DB->table('categories')." c ON a.cate_id=c.cate_id WHERE $where LIMIT 1");
	
	return $row;
}

/** prev article */
function get_prev_article($aid = 0) {
	global $DB;
	
	$row = $DB->fetch_one("SELECT art_id, art_title FROM ".$DB->table('articles')." WHERE art_status=3 AND art_id < $aid ORDER BY art_id DESC LIMIT 1");
	if (!empty($row)) {
		$row['art_link'] = get_article_url($row['art_id']);
	}
	
	return $row;
}

/** next article */
function get_next_article($aid = 0) {
	global $DB;
	
	$row = $DB->fetch_one("SELECT art_id, art_title FROM ".$DB->table('articles')." WHERE art_status=3 AND art_id > $aid ORDER BY art_id ASC LIMIT 1");
	if (!empty($row)) {
		$row['art_link'] = get_article_url($row['art_id']);
	}
	
	return $row;
}

/** rssfeed */
function get_article_rssfeed($cate_id = 0) {
	global $DB, $options;
		
	$where = "a.art_status=3 AND c.cate_mod='article'";
	$cate = get_one_category($cate_id);
	if (!empty($cate)) {
		if ($cate['cate_childcount'] > 0) {
			$where .= " AND a.cate_id IN (".$cate['cate_arrchildid'].")";
		} else {
			$where .= " AND a.cate_id=$cate_id";
		}
	}

	$sql = "SELECT a.art_id, a.cate_id, a.art_title, a.art_intro, a.art_ctime, c.cate_name FROM ".$DB->table('articles')." a LEFT JOIN ".$DB->table('categories')." c ON a.cate_id=c.cate_id";
	$sql .= " WHERE $where ORDER BY a.art_id DESC LIMIT 50";
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		$row['art_link'] = str_replace('&', '&amp;', get_article_url($row['art_id'], true));
		$row['art_intro'] = htmlspecialchars(strip_tags($row['art_intro']));
		$row['art_ctime'] = date('Y-m-d H:i:s', $row['art_ctime']);
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
		echo "<link>".$row['art_link']."</link>\n";
		echo "<title>".$row['art_title']."</title>\n";
		echo "<author>".$options['site_name']."</author>\n";
		echo "<category>".$row['cate_name']."</category>\n";
		echo "<pubDate>".$row['art_ctime']."</pubDate>\n";
		echo "<guid>".$row['art_link']."</guid>\n";
		echo "<description>".$row['art_intro']."</description>\n";
		echo "</item>\n";
	}
	echo "</channel>\n";
	echo "</rss>";
	
	unset($options, $results);
}
	
/** sitemap */
function get_article_sitemap($cate_id = 0) {
	global $DB, $options;
	
	$where = "art_status=3";
	$cate = get_one_category($cate_id);
	if (!empty($cate)) {
		if ($cate['cate_childcount'] > 0) {
			$where .= " AND cate_id IN (".$cate['cate_arrchildid'].")";
		} else {
			$where .= " AND cate_id=$cate_id";
		}
	}

	$sql = "SELECT art_id, art_ctime FROM ".$DB->table('articles');
	$sql .= " WHERE $where ORDER BY art_id DESC LIMIT 50";
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		$row['art_link'] = str_replace('&', '&amp;', get_article_url($row['art_id'], true));
		$row['art_ctime'] = date('Y-m-d H:i:s', $row['art_ctime']);
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
		
		if (datediff('h', $row['art_ctime']) < 24) {
			$freq = "hourly";
			$prior = 0.8;
		} elseif (datediff('d', $row['art_ctime']) < 7) {
			$freq = "daily";
			$prior = 0.7;
		} elseif (datediff('w', $row['art_ctime']) < 4) {
			$freq = "weekly";
		} elseif (datediff('m', $row['art_ctime']) < 12) {
			$freq = "monthly";
		} else {
			$freq = "yearly";
		}
		
		echo "<url>\n";
		echo "<loc>".$row['art_link']."</loc>\n";
		echo "<lastmod>".iso8601('Y-m-d\TH:i:s\Z', $row['art_ctime'])."</lastmod>\n";
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
function get_article_api($cate_id = 0, $start = 0, $pagesize = 0) {
	global $DB, $options;
		
	$where = "a.art_status=3 AND c.cate_mod='article'";
	$cate = get_one_category($cate_id);
	if (!empty($cate)) {
		if ($cate['cate_childcount'] > 0) {
			$where .= " AND a.cate_id IN (".$cate['cate_arrchildid'].")";
		} else {
			$where .= " AND a.cate_id=$cate_id";
		}
	}

	$sql = "SELECT a.art_id, a.cate_id, a.art_title, a.art_tags, a.art_intro, a.art_content, a.art_views, a.art_ctime, c.cate_name FROM ".$DB->table('articles')." a LEFT JOIN ".$DB->table('categories')." c ON a.cate_id=c.cate_id";
	$sql .= " WHERE $where ORDER BY a.art_id DESC LIMIT $start, $pagesize";
	$query = $DB->query($sql);
	$results = array();
	while ($row = $DB->fetch_array($query)) {
		$row['art_link'] = str_replace('&', '&amp;', get_article_url($row['art_id'], true));
		$row['art_intro'] = htmlspecialchars(strip_tags($row['art_intro']));
		$row['art_ctime'] = date('Y-m-d H:i:s', $row['art_ctime']);
		$results[] = $row;
	}
	unset($row);
	$DB->free_result($query);
	
	$total = $DB->get_count($DB->table('articles').' a', $where);
	
	header("Content-Type: application/xml;");
	echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
	echo "<urlset xmlns=\"http://www.sodir.org/sitemap/\">\n";
	echo "<total>".$total."</total>";
	
	foreach ($posts as $row) {
		echo "<url>\n";
		echo "<name>".$row['art_title']."</name>\n";
		echo "<link>".$row['art_link']."</link>\n";
		echo "<tags>".$row['art_tags']."</tags>\n";
		echo "<desc>".$row['art_intro']."</desc>\n";
		echo "<content><!--CDATA[".$row['art_content']."]--></desc>\n";
		echo "<cate>".$row['cate_name']."</cate>\n";
		echo "<time>".$row['art_ctime']."</time>\n";		
		echo "</url>\n";
	}
	echo "</urlset>\n";
	
	unset($options, $results);
}
?>