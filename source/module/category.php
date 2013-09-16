<?php
/** category path */
function get_category_path($cate_id = 0, $separator = ' &raquo; ') {
	global $DB;
	
	$cate = get_one_category($cate_id);
	if (!isset($cate)) return '';
	
	$sql = "SELECT cate_id, cate_mod, cate_name FROM ".$DB->table('categories')." WHERE cate_id IN (".$cate_id.','.$cate['cate_arrparentid'].")";
	$categories = $DB->fetch_all($sql);
	foreach ($categories as $row) {
		$strpath .= $separator.'<a href="'.get_category_url($row['cate_mod'], $row['cate_id']).'">'.$row['cate_name'].'</a>';
	}
	unset($cate, $categories);
	
	return $strpath;
}
	
/** category option */
function get_category_option($cate_mod = 'webdir', $root_id = 0, $cate_id = 0, $level_id = 0) {
	global $DB;
	
	if (!in_array($cate_mod, array('webdir', 'article'))) $cate_mod = 'webdir';
	$where = "root_id=$root_id AND cate_mod='$cate_mod'";
	$sql = "SELECT cate_id, cate_name FROM ".$DB->table('categories')." WHERE $where ORDER BY cate_order ASC, cate_id ASC";
	$results = $DB->fetch_all($sql);
	$optstr = '';
	foreach ($results as $row) {
		$optstr .= '<option value="'.$row['cate_id'].'"';
		if ($cate_id > 0 && $cate_id == $row['cate_id']) $optstr .= ' selected';
		
		if ($level_id == 0) {
			$optstr .= ' style="background: #EEF3F7;">';
			$optstr .= '├'.$row['cate_name'];
		} else {
			$optstr .= '>';
			for ($i = 2; $i <= $level_id; $i++) {
				$optstr .= '│&nbsp;&nbsp;';
			}
			$optstr .= '│&nbsp;&nbsp;├'.$row['cate_name'];
		}
		$optstr .= '</option>';
		$optstr .= get_category_option($cate_mod, $row['cate_id'], $cate_id, $level_id + 1);
	}
	unset($results);
		
	return $optstr;
}

/** categories */
function get_categories($cate_id = 0, $top_num = 0) {
	global $DB;
	
	$sql = "SELECT cate_id, cate_mod, cate_name, cate_childcount, cate_postcount FROM ".$DB->table('categories')." WHERE root_id=$cate_id ORDER BY cate_order ASC, cate_id ASC";
	if ($top_num > 0) $sql .= " LIMIT $top_num";
	$results = $DB->fetch_all($sql);
	$categories = array();
	foreach ($results as $row) {
		$row['cate_link'] = get_category_url($row['cate_mod'], $row['cate_id']);
		$categories[] = $row;
	}
	unset($results);
	
	return $categories;
}

/** best categories */
function get_best_categories($top_num = 0) {
	global $DB;
	
	$sql = "SELECT cate_id, cate_mod, cate_name, cate_childcount, cate_postcount FROM ".$DB->table('categories')." WHERE cate_isbest=1 ORDER BY cate_order ASC, cate_id ASC";
	if ($top_num > 0) $sql .= " LIMIT $top_num";
	$results = $DB->fetch_all($sql);
	$categories = array();
	foreach ($results as $row) {
		$row['cate_link'] = get_category_url($row['cate_mod'], $row['cate_id']);
		$categories[] = $row;
	}
	unset($results);
	
	return $categories;
}

/** all category */
function get_all_category() {
	global $DB;
	
	$results = load_cache('categories') ? load_cache('categories') : $DB->fetch_all("SELECT cate_id, root_id, cate_mod, cate_name, cate_dir, cate_arrparentid, cate_arrchildid, cate_childcount, cate_postcount FROM ".$DB->table('categories')." ORDER BY cate_order ASC, cate_id ASC");
		
	$categories = array();
	foreach ($results as $row) {
		$row['cate_link'] = get_category_url($row['cate_mod'], $row['cate_id']);
		$categories[$row['cate_id']] = $row;
	}
	unset($results);
	
	return $categories;
}

/** one category */
function get_one_category($cate_id = 0) {
	global $DB;
	
	$row = load_cache('category_'.$cate_id) ? load_cache('category_'.$cate_id) : $DB->fetch_one("SELECT cate_id, root_id, cate_mod, cate_name, cate_dir, cate_arrparentid, cate_arrchildid, cate_childcount, cate_postcount FROM ".$DB->table('categories')." WHERE cate_id=$cate_id LIMIT 1");
		
	return $row;
}
	
/** category name */
function get_category_name($cate_id) {
	$category = get_one_category($cate_id);
	return $category['cate_name'];
}
	
/** category count */
function get_category_count($cate_id = 0) {
	global $DB;
	
	if ($cate_id > 0) $where = array('root_id' => $cate_id);
	$count = $DB->get_count($DB->table('categories'), $where);
		
	return $count;
}
	
/** category parent ids */
function get_category_parent_ids($cate_id) {
	global $DB;
	
	$sql = "SELECT root_id FROM ".$DB->table('categories')." WHERE cate_id=$cate_id";
	$results = $DB->fetch_all($sql);		
	$idstr = '';
	foreach ($results as $row) {
		if ($row['root_id'] > 0) {
			$idstr .= get_category_parent_ids($row['root_id']);
			$idstr .= ','.$row['root_id'];
		} else {
			$idstr = '0';
		}
	}
	unset($results);
		
	return $idstr;
}

/** category child ids */
function get_category_child_ids($cate_id) {
	global $DB;
	
	$sql = "SELECT cate_id FROM ".$DB->table('categories')." WHERE root_id=$cate_id";
	$results = $DB->fetch_all($sql);	
	$idstr = '';
	foreach ($results as $row) {
		$idstr .= ','.$row['cate_id'];
		$idstr .= get_category_child_ids($row['cate_id']);
	}
	unset($results);
	
	return $idstr;
}

/** category model option */
function get_category_model_option($model = 'website') {
	global $category_modules;
	
	foreach ($category_modules as $key => $val) {
		$optstr .= '<option value="'.$key.'"';
		if (!empty($model) && $model == $key) $optstr .= ' selected';
		$optstr .= '>'.$val.'</option>';
	}
	unset($category_modules);
		
	return $optstr;
}
?>