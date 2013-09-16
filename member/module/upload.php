<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

require(APP_PATH.'include/upload.php');
require(APP_PATH.'include/json.php');

if ($_GET['act'] == 'upload') {
	$savepath = '../'.$options['upload_dir'].'/article/';
	
	$upload = new upload_file();
	$upload->make_dir($savepath);
	$upload->init($_FILES['imgFile'], $savepath);
	
	header('Content-type: text/html; charset=utf-8');
	$json = new Services_JSON();
	
	if ($upload->error_code == 0) {
		$upload->save_file();
		echo $json->encode(array('error' => 0, 'url' => $upload->attach['path']));
		exit;
	} else {
		echo $json->encode(array('error' => 1, 'message' => $upload->error()));
		exit;
	}
}
?>