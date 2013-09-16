<?php
/** write cache */
function write_cache($cache_name, $cache_data = '') {
	$cache_dir = ROOT_PATH.'data/static/';
	$cache_file = $cache_dir.$cache_name.'.php';
	
	if (!is_dir($cache_dir)) {
		@mkdir($cache_dir, 0777);
	}
	
	if ($fp = @fopen($cache_file, 'wb')) {
		@fwrite($fp, "<?php\r\n//File name: ".$cache_name.".php\r\n//Creation time: ".date('Y-m-d H:i:s')."\r\n\r\nif (!defined('IN_HANFOX')) exit('Access Denied');\r\n\r\n".$cache_data."\r\n?>");
		@fclose($fp);
		@chmod($cache_file, 0777);
	} else {
		echo 'Error: Can\'t write to '.$cache_name.' cache files, please check directory.!';
		exit;
	}
}

/** load cache */
function load_cache($cache_name) {
	static $static_data = array();
	if (!empty($cache_name)) {
		$cache_file = ROOT_PATH.'data/static/'.$cache_name.'.php';
		if (is_file($cache_file)) {
			@require($cache_file);
			return $static_data;
		} else {
			return false;
		}
	}
}

/** update cache */
function update_cache($cache_name = '') {
	$update_list = empty($cache_name) ? array() : (is_array($cache_name) ? $cache_name : array($cache_name));
	foreach ($update_list as $entry) {
		call_user_func($entry.'_cache');
	}
	unset($update_list);
}
?>