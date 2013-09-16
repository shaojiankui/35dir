<?php
require(APP_PATH.'extend/smarty/Smarty.class.php');

$template_dir = ROOT_PATH.THEME_DIR.'/';
$compile_dir = ROOT_PATH.'data/compile/';
$cache_dir = ROOT_PATH.'data/cache/';
		
if (defined('IN_ADMIN') && IN_ADMIN == TRUE) {
	$dirname = 'system';
	$lifetime = 0;
	$caching = false;
} elseif (defined('IN_MEMBER')) {
	$dirname = 'member';
	$lifetime = 0;
	$caching = false;
} else {
	$dirname = 'default';
	$lifetime = CACHE_LIFETIME;
	$caching = CACHE_ON;
}

$smarty = new Smarty();
$smarty->debugging = false;
$smarty->template_dir = $template_dir.$dirname.'/';
$smarty->compile_dir = $compile_dir.$dirname.'/';
$smarty->cache_dir = $cache_dir.$dirname.'/';
$smarty->caching = $caching;
$smarty->cache_lifetime = $lifetime;
$smarty->left_delimiter = "{#";
$smarty->right_delimiter = "#}";

/** check if a template resource exists */
function template_exists($template) {
	global $smarty;
	
	if (!$smarty->templateExists($template)){
		exit('The template file "'.$template.'" is not found!');
	}
}
?>