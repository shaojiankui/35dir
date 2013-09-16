<?php
require('common.php');

$pagetitle = SYS_NAME.SYS_VERSION;

$smarty->assign('site_root', str_replace('\\', '/', dirname(HF_ROOT)));
$smarty->assign('pagetitle', $pagetitle);
$smarty->display('admin.html');
?>