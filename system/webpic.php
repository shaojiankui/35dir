<?php
require('common.php');

$fileurl = 'webpic.php';
$table = $DB->table('websites');
$type = trim($_GET['type']);

/** page */
$curpage = intval($_GET['page']);
if ($curpage > 1) {
	$start = ($curpage - 1) * $pagesize;
} else {
	$start = 0;
	$curpage = 1;
}

/** download */
if ($action == 'down') {
	$pagesize = 5;
	$curpage = $curpage + 1;

	$where = "web_status=3";
	if ($type == 'part') {
		$where .= " AND web_pic=''";
	}
	
	$websites = $DB->fetch_all("SELECT web_id, web_name, web_url FROM $table WHERE $where ORDER BY web_id DESC LIMIT $start, $pagesize");
	$totalnum = $DB->get_count($table, $where);
	$totalpage = @ceil($totalnum / $pagesize);
	
	echo '<div style="font-size: 12px; line-height: 25px; padding: 10px;">';
	if ($curpage <= $totalpage) {
		$savepath = '../'.$options['upload_dir'].'/website/';
		
		echo '<meta http-equiv="refresh" content=3;url="'.$fileurl.'?act='.$action.'&type='.$type.'&page='.$curpage.'">';
		echo '<h3>共需采集 '.$totalpage.' 页，每次下载 '.$pagesize.' 张，当前第 '.$curpage.' 页，正在下载远程图片...</h3>';
		foreach ($websites as $row) {
			$filepath = save_to_local(str_replace('/', '.', $row['web_url']), $savepath);
			$newpath = str_replace('../uploads/', '', $filepath);
			
			if (!empty($newpath)) {
				$status = '下载成功！';
				$DB->update($table, array('web_pic' => $newpath), array('web_id' => $row['web_id']));
			} else {
				$status = '下载失败！';
			}
			echo $row['web_id'].' - '.$row['web_name'].' ------ '.$status.'<br />';
		}
		echo '<h3>本页已采集完成，5秒后将自动采集下一页...<h3>';
	} else {
		echo '<h3>已经将所有的远程图片本地化!</h3>';	
	}
	echo '</div>';
}

/** check */
if ($action == 'check') {
	$pagesize = 1000;
	$curpage = $curpage + 1;

	$where = "web_status=3";
	$websites = $DB->fetch_all("SELECT web_id, web_name, web_pic FROM $table WHERE $where ORDER BY web_id DESC LIMIT $start, $pagesize");
	$totalnum = $DB->get_count($table, $where);
	$totalpage = @ceil($totalnum / $pagesize);
	
	echo '<div style="font-size: 12px; line-height: 25px; padding: 10px;">';
	if ($curpage <= $totalpage) {
		$savepath = '../'.$options['upload_dir'].'/';
		
		echo '<meta http-equiv="refresh" content=3;url="'.$fileurl.'?act='.$action.'&page='.$curpage.'">';
		echo '<h3>总共 '.$totalpage.' 页，每次检测 '.$pagesize.' 条，正在检测第 '.$curpage.' 页...</h3>';
		foreach ($websites as $row) {
			if (!file_exists($savepath.$row['web_pic'])) {
				$status = '图片不存在！';
				$DB->update($table, array('web_pic' => ''), array('web_id' => $row['web_id']));
			} else {
				$status = '图片存在！';
			}
			echo $row['web_id'].' - '.$row['web_name'].' ------ '.$status.'<br />';
		}
		echo '<h3>本页已检测完成，3秒后将自动检测下一页...<h3>';
	} else {
		echo '<h3>所有站点检测成功!</h3>';
	}
	echo '</div>';
}