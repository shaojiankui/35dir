<?php
require('common.php');
require(APP_PATH.'include/databak.php');

$fileurl = 'database.php';
$tempfile = 'database.html';

$DBak = new DataBak(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_CHARSET);
$datadir = $DBak->datadir;

if (!isset($action)) $action = 'backup';

/** backup */
if ($action == 'backup') {
	$pagetitle = '数据库备份';
	$tabels = $DBak->get_tables();
	
	$smarty->assign('tables', $tabels);
	$smarty->assign('h_action', 'do_backup');
}

/** restore */
if ($action == 'restore') {
	$pagetitle = '数据库恢复';
			
	$i = 0;
	if (is_dir($datadir)) {
		$dirs = dir($datadir);
		$files = array();
		$today = date('Y-m-d',time());
		while ($file = $dirs->read()) {
			$filepath = $datadir.'/'.$file;
			$pathinfo = pathinfo($file);
			if (is_file($filepath) && $pathinfo['extension'] == 'php') {
				$moday = date('Y-m-d', @filemtime($filepath));
				$mtime = date('Y-m-d H:i', @filemtime($filepath));
						
				$fileinfo = array(
					'filename' => htmlspecialchars($file),
					'filesize' => get_real_size(filesize($filepath)),
					'filemtime' => ($moday == $today) ? '<font color="#FF0000">'.$mtime.'</font>' : $mtime,
					'filepath' => urlencode($file),
					'fileoper' => '<a href="'.$fileurl.'?act=import&file='.$file.'" onClick="return confirm(\'确认导入此文件吗？\')">导入</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?act=delete&file='.$file.'" onClick="return confirm(\'确认删除此文件吗？\n注：删除后将无法恢复！\')">删除</a>',
				);
				$i++;
				$files[] = $fileinfo;
			}
		}
		unset($fileinfo);
		$dirs->close();
	}
			
	$smarty->assign('files', $files);
	$smarty->assign('h_action', 'do_restore');
}

/** maintain */
if ($action == 'maintain') {
	$pagetitle = '数据库维护';
	
	$smarty->assign('h_action', 'do_maintain');
}

/** dbinfo */
if ($action == 'dbinfo') {
	$pagetitle = '数据库信息';
	
	$mysql_version = mysql_get_server_info();
	$mysql_runtime = '';
	$query = $DB->query("SHOW STATUS");
	while ($row = $DB->fetch_array($query)) {
		if (preg_match("/^uptime+$/i", $row['Variable_name'])){
			$mysql_runtime = $row['Value'];
		}
	}
	$mysql_runtime = format_timespan($mysql_runtime);

	$query = $DB->query("SHOW TABLE STATUS");
	$table_num = $table_rows = $data_size = $index_size = $free_size = 0;
	$tables = array();
	
	while ($table = $DB->fetch_array($query)) {
		$data_size = $data_size + $table['Data_length'];
		$index_size = $index_size + $table['Index_length'];
		$table_rows = $table_rows + $table['Rows'];
		$free_size = $free_size + $table['Data_free'];
		
		$table['Data_length'] = get_real_size($table['Data_length']);
		$table['Index_length'] = get_real_size($table['Index_length']);
		$table['Data_free'] = $table['Data_free'] > 0 ? '<font color="#ff0000">'.get_real_size($table['Data_free']).'</font>' : get_real_size($table['Data_free']);
		$table_num++;
		$tables[] = $table;
	}
	unset($table);
	
	$data_size = get_real_size($data_size);
	$index_size = get_real_size($index_size);
	$free_size = get_real_size($free_size);
	
	$smarty->assign('tables', $tables);
	$smarty->assign('data_size', $data_size);
	$smarty->assign('index_size', $index_size);
	$smarty->assign('free_size', $free_size);
	$smarty->assign('table_num', $table_num);
	$smarty->assign('h_action', 'do_maintain');
}

/** do backup */
if ($action == 'do_backup') {
	$baktype = trim($_POST['baktype']);
	$tables = (array) $_POST['table'];
	$volsize = intval($_POST['volsize']);
	
	if ($volsize <= 0) {
		msgbox('分卷文件大小不能小于0！');
	}
	
	if ($baktype == 'full') {
		if ($DBak->export_sql('', $volsize)) {
			msgbox('数据备份成功！');
		} else {
			msgbox('数据备份失败！');
		}
	}
	
	if ($baktype == 'custom') {
		if (empty($tables)) {
			msgbox('请选择您要备份的数据表！');
		}
		
		if ($DBak->export_sql($tables, $volsize)) {
			msgbox('数据备份成功！');
		} else {
			msgbox('数据备份失败！');
		}		
	}
}

/** do restore */
if ($action == 'import') {
	$bakfile = trim($_GET['file']);
	$filepath = $DBak->datadir.$bakfile;
	
	if (empty($bakfile)) {
		msgbox('请指定要恢复的文件！');
	}
	
	if ($DBak->import_sqlfile($filepath)) {
		msgbox('数据恢复成功！');
	} else {
		msgbox('数据恢复失败！');
	}
}

/** do delete */
if ($action == 'delete') {
	$bakfile = trim($_GET['file']);
	$filepath = $DBak->datadir.$bakfile;
	
	if (unlink($filepath)) {
		msgbox('文件删除成功！', $fileurl.'?act=restore');
	} else {
		msgbox('文件删除失败！');
	}
}

/** do maintain */
if ($action == 'do_maintain') {
	$doname = array (
		'check' => '检查',
		'repair' => '修复',
		'analyze' => '分析',
		'optimize' => '优化',
	);
	$tables = $DBak->get_tables();
	
	$doarr = $_POST['do'];
	if (empty($doarr)) {
		msgbox('请选择你要执行的操作！');
	}
	
	foreach ($doarr as $do) {
		foreach ($tables as $table) {
			if ($DB->query($do.' TABLE '.$table)) {
				$result .= $doname[$do].'“'.$table.'”-----------------------------------<font color="#008800">成功！</font><br />';
			} else {
				$result .= $doname[$do].'“'.$table.'”-----------------------------------<font color="#ff0000">失败！</font><br />';
			}
		}
	}
	msgbox($result);
}

smarty_output($tempfile);
?>