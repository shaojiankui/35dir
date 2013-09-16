<?php
/** module */
function get_module_url($module = 'index') {
	global $options;
	
	if ($module == 'index') {
		$strurl = $options['site_root'];
	} else {
		if ($options['link_struct'] == 1) {
			$strurl = $options['site_root'].$module.'.html';
		} elseif ($options['link_struct'] == 2) {
			$strurl = $options['site_root'].$module.'/';
		} elseif ($options['link_struct'] == 3) {
			$strurl = $options['site_root'].$module;
		} else {
			$strurl = '?mod='.$module;
		}
	}
	
	return $strurl;
}

/** category */
function get_category_url($cate_mod = 'webdir', $cate_id = 0, $page = 1) {
	global $options;
	
	$cate = get_one_category($cate_id);
	$cate_dir = !empty($cate['cate_dir']) ? $cate['cate_dir'] : 'category';
	$page = isset($page) && $page > 0 ? $page : 1;
	
	if ($options['link_struct'] == 1) {
		$strurl = $options['site_root'].$cate_mod.'-'.$cate_dir.'-'.$cate_id.'-'.$page.'.html';
	} elseif ($options['link_struct'] == 2) {
		$strurl = $options['site_root'].$cate_mod.'/'.$cate_dir.'/'.$cate_id.'-'.$page.'.html';
	} elseif ($options['link_struct'] == 3) {
		$strurl = $options['site_root'].$cate_mod.'/'.$cate_dir.'/'.$cate_id.'/'.$page;
	} else {
		$strurl = '?mod='.$cate_mod.'&cid='.$cate_id;
	}
	unset($cate);
	
	return $strurl;
}

/** update */
function get_update_url($days, $page = 1) {
	global $options;
	
	$days = isset($days) && $days > 0 ? $days : 0;
	$page = isset($page) && $page > 0 ? $page : 1;
	
	if ($options['link_struct'] == 1) {
		$strurl = $options['site_root'].'update-'.$days.'-'.$page.'.html';
	} elseif ($options['link_struct'] == 2) {
		$strurl = $options['site_root'].'update/'.$days.'-'.$page.'.html';
	} elseif ($options['link_struct'] == 3) {
		$strurl = $options['site_root'].'update/'.$days.'/'.$page;
	} else {
		$strurl = '?mod=update&days='.$days;
	}
	
	return $strurl;
}

/** archives */
function get_archives_url($date, $page = 1) {
	global $options;
	
	$date = isset($date) && strlen($date) == 6 ? $date : 0;
	$page = isset($page) && $page > 0 ? $page : 1;
	
	if ($options['link_struct'] == 1) {
		$strurl = $options['site_root'].'archives-'.$date.'-'.$page.'.html';
	} elseif ($options['link_struct'] == 2) {
		$strurl = $options['site_root'].'archives/'.$date.'-'.$page.'.html';
	} elseif ($options['link_struct'] == 3) {
		$strurl = $options['site_root'].'archives/'.$date.'/'.$page;
	} else {
		$strurl = '?mod=archives&date='.$date;
	}
	
	return $strurl;
}

/** search */
function get_search_url($type = 'name', $query, $page = 1) {
	global $options;

	$query = isset($query) && !empty($query) ? urlencode($query) : '';
	$page = isset($page) && $page > 0 ? $page : 1;
	
	if ($options['link_struct'] == 1) {
		$strurl = $options['site_root'].'search/'.$type.'-'.$query.'-'.$page.'.html';
	} elseif ($options['link_struct'] == 2) {
		$strurl = $options['site_root'].'search/'.$type.'/'.$query.'-'.$page.'.html';
	} elseif ($options['link_struct'] == 3) {
		$strurl = $options['site_root'].'search/'.$type.'/'.$query.'/'.$page;
	} else {
		$strurl = '?mod=search&type='.$type.'&query='.$query;
	}
	
	return $strurl;
}

/** website */
function get_website_url($web_id, $abs_path = false) {
	global $options;
	
	if ($abs_path) {
		$url_prefix = $options['site_url'];
	} else {
		$url_prefix = $options['site_root'];
	}
	
	if ($options['link_struct'] == 1) {
		$strurl = $url_prefix.'siteinfo-'.$web_id.'.html';
	} elseif ($options['link_struct'] == 2) {
		$strurl = $url_prefix.'siteinfo/'.$web_id.'.html';
	} elseif ($options['link_struct'] == 3) {
		$strurl = $url_prefix.'siteinfo/'.$web_id;
	} else {
		$strurl = $url_prefix.'?mod=siteinfo&wid='.$web_id;
	}
	
	return $strurl;
}

/** article */
function get_article_url($art_id, $abs_path = false) {
	global $options;
	
	if ($abs_path) {
		$url_prefix = $options['site_url'];
	} else {
		$url_prefix = $options['site_root'];
	}
	
	if ($options['link_struct'] == 1) {
		$strurl = $url_prefix.'artinfo-'.$art_id.'.html';
	} elseif ($options['link_struct'] == 2) {
		$strurl = $url_prefix.'artinfo/'.$art_id.'.html';
	} elseif ($options['link_struct'] == 3) {
		$strurl = $url_prefix.'artinfo/'.$art_id;
	} else {
		$strurl = $url_prefix.'?mod=artinfo&aid='.$art_id;
	}
	
	return $strurl;
}

/** weblink */
function get_weblink_url($link_id, $abs_path = false) {
	global $options;
	
	if ($abs_path) {
		$url_prefix = $options['site_url'];
	} else {
		$url_prefix = $options['site_root'];
	}
	
	if ($options['link_struct'] == 1) {
		$strurl = $url_prefix.'linkinfo-'.$link_id.'.html';
	} elseif ($options['link_struct'] == 2) {
		$strurl = $url_prefix.'linkinfo/'.$link_id.'.html';
	} elseif ($options['link_struct'] == 3) {
		$strurl = $url_prefix.'linkinfo/'.$link_id;
	} else {
		$strurl = $url_prefix.'?mod=linkinfo&lid='.$link_id;
	}
	
	return $strurl;
}

/** diypage */
function get_diypage_url($page_id) {
	global $options;
	
	if ($options['link_struct'] == 1) {
		$strurl = $options['site_root'].'diypage-'.$page_id.'.html';
	} elseif ($options['link_struct'] == 2) {
		$strurl = $options['site_root'].'diypage/'.$page_id.'.html';
	} elseif ($options['link_struct'] == 3) {
		$strurl = $options['site_root'].'diypage/'.$page_id;
	} else {
		$strurl = '?mod=diypage&pid='.$page_id;
	}
	
	return $strurl;
}

/** rssfeed */
function get_rssfeed_url($module, $cate_id) {
	global $options;
	
	if ($cate_id > 0) {
		if ($options['link_struct'] == 1) {
			$strurl = $options['site_root'].'rssfeed-'.$module.'-'.$cate_id.'.html';
		} elseif ($options['link_struct'] == 2) {
			$strurl = $options['site_root'].'rssfeed/'.$module.'/'.$cate_id.'.html';
		} elseif ($options['link_struct'] == 3) {
			$strurl = $options['site_root'].'rssfeed/'.$module.'/'.$cate_id;
		} else {
			$strurl = '?mod=rssfeed&type='.$module.'&cid='.$cate_id;
		}
	} else {
		if ($options['link_struct'] == 1) {
			$strurl = $options['site_root'].'rssfeed-'.$module.'.html';
		} elseif ($options['link_struct'] == 2) {
			$strurl = $options['site_root'].'rssfeed/'.$module.'/';
		} elseif ($options['link_struct'] == 3) {
			$strurl = $options['site_root'].'rssfeed/'.$module;
		} else {
			$strurl = '?mod=rssfeed&type='.$module;
		}
	}
	
	return $strurl;
}

/** sitemap */
function get_sitemap_url($module, $cate_id) {
	global $options;
	
	if ($cate_id > 0) {
		if ($options['link_struct'] == 1) {
			$strurl = $options['site_root'].'sitemap-'.$module.'-'.$cate_id.'.html';
		} elseif ($options['link_struct'] == 2) {
			$strurl = $options['site_root'].'sitemap/'.$module.'/'.$cate_id.'.html';
		} elseif ($options['link_struct'] == 3) {
			$strurl = $options['site_root'].'sitemap/'.$module.'/'.$cate_id;
		} else {
			$strurl = '?mod=sitemap&type='.$module.'&cid='.$cate_id;
		}
	} else {
		if ($options['link_struct'] == 1) {
			$strurl = $options['site_root'].'sitemap-'.$module.'.html';
		} elseif ($options['link_struct'] == 2) {
			$strurl = $options['site_root'].'sitemap/'.$module.'/';
		} elseif ($options['link_struct'] == 3) {
			$strurl = $options['site_root'].'sitemap/'.$module;
		} else {
			$strurl = '?mod=sitemap&type='.$module;
		}
	}
	
	return $strurl;
}

/** thumbs */
function get_webthumb($web_pic) {
	global $options;
	
	if (!empty($web_pic)) {
		$strurl = $options['site_root'].$options['upload_dir'].'/'.$web_pic;
	} else {
		$strurl = $options['site_root'].'public/images/nopic.gif';
	}
	
	return $strurl;
}
?>