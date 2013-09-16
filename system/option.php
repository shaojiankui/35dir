<?php
require('common.php');

$fileurl = 'option.php';
$tempfile = 'option.html';
$table = $DB->table('options');

$option = $_GET['opt'] ? $_GET['opt'] : $_POST['opt'];
if (!isset($option)) $option = 'basic';
$fileurl .= '?opt='.$option;

if (in_array($option, array('basic', 'misc', 'user', 'link', 'mail'))) {
	switch ($option) {
		case 'basic' :
			$pagetitle = '站点信息';
			break;
		case 'misc' :
			$pagetitle = '选项设置';
			break;
		case 'user' :
			$pagetitle = '注册设置';
			break;
		case 'link' :
			$pagetitle = '链接设置';
			break;
		case 'mail' :
			$pagetitle = '邮件设置';
			break;
		default :
			$pagetitle = '站点信息';
			break;
	}
	
	$configs = stripslashes_deep($options);
	$configs['site_root'] = str_replace('\\', '/', dirname($site_root));
	
	$smarty->assign('pagetitle', $pagetitle);
	$smarty->assign('option', $option);
	$smarty->assign('cfg', $configs);
	unset($configs);
	
	if ($action == 'update') {
		foreach ($_POST['cfg'] as $cname => $cval) {
			if ($cname == 'site_url' && !empty($cval)) $cval .= (substr($cval, -1) != '/') ? '/' : '';
			if ($cname == 'data_update_cycle' && $cval <= 0) $cval = 3;
			if ($cname == 'filter_words') {
				$cval = str_replace('，', ',', $cval);
				$cval = str_replace(',,', ',', $cval);
				if (substr($cval, -1) == ',') {
					$cval = substr($cval, 0, strlen($cval) - 1);
				}
			}
			
			$udata = array('option_value' => $cval);
			$uwhere = array('option_name' => $cname);
			$idata = array('option_name' => $cname, 'option_value' => $cval);
			
			$DB->fetch_one("SELECT option_name FROM $table WHERE option_name = '$cname'") ? $DB->update($table, $udata, $uwhere) : $DB->insert($table, $idata);
		}
		update_cache('options');
		
		msgbox('更新系统配置成功！', $fileurl);
	}
}

smarty_output($tempfile);
?>