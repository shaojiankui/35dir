<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '网站认领';
$pageurl = '?mod=claim';
$tplfile = 'claim.html';
$table = $DB->table('websites');

$action = isset($_GET['act']) ? $_GET['act'] : 'one';
$smarty->assign('action', $action); 

if (!$smarty->isCached($tplfile)) {
	$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
	$smarty->assign('site_path', get_sitepath().' &raquo; '.$pagename);
	
	if ($_POST['do'] == 'next') {
		$domain = strtolower(trim($_POST['domain']));
		
		if (empty($domain)) {
			msgbox('请输入要认领的域名！');
		} else {
			if (!is_valid_domain($domain)) {
				msgbox('请输入正确的网站域名！');
			}
		}
		
    	$query = $DB->query("SELECT web_id FROM $table WHERE web_url='$domain'");
    	if (!$DB->num_rows($query)) {
        	msgbox('该网站还未被提交！', '?mod=website&act=add');
    	}
		
		$smarty->assign('action', 'two'); 
		$smarty->assign('domain', $domain);
		$smarty->assign('siteurl', format_url($domain));
		$smarty->assign('token', random(32));
		
	} elseif ($_POST['do'] == 'verify') {
		$vtype = trim($_POST['vtype']);
		$domain = strtolower(trim($_POST['domain']));
		$token = trim($_POST['token']);
		
		if (empty($vtype)) {
			msgbox('请选择验证类型！');
		}
		
		if (empty($domain)) {
			msgbox('请输入要认领的域名！');
		} else {
			if (!is_valid_domain($domain)) {
				msgbox('请输入正确的网站域名！');
			}
		}
		
    	$query = $DB->query("SELECT web_id FROM $table WHERE web_url='$domain'");
    	if (!$DB->num_rows($query)) {
        	msgbox('该网站还未被提交！');
    	}
		
		$siteurl = format_url($domain);
		if ($vtype == 'file') {
			$content = get_url_content($siteurl.'35dir-site-verification.html');
			if ($content == $token) {
				$DB->update($table, array('user_id' => $myself['user_id']), array('web_url' => $domain));
				msgbox('网站认领成功！', '?mod=website');
			} else {
				msgbox('网站验证失败！');
			}
		}
		
		if ($vtype == 'meta') {
			$content = get_url_content($siteurl);
			if (preg_match('/<meta\s+name=\"35dir-site-verification\"\s+content=\"(.*?)\"/si', $content, $matches)) {
				if ($matches[1] == $token) {
					$DB->update($table, array('user_id' => $myself['user_id']), array('web_url' => $domain));
					msgbox('网站认领成功！', '?mod=website');					
				} else {
					msgbox('网站验证失败！');
				}
			} else {
				msgbox('您还未向首页添加元标记！');
			}
		}
	}
}

smarty_output($tplfile);
?>