<?php
function get_options() {
	global $DB;
	
	$options = array();
	$results = $DB->fetch_all("SELECT option_name, option_value FROM ".$DB->table('options'));
	foreach ($results as $row) {
		$options[$row['option_name']] = addslashes($row['option_value']);
	}
	unset($results);
	
	return load_cache('options') ? load_cache('options') : $options;
}
?>