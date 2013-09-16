<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

setcookie('auth_cookie', '', time() - 31536000, $options['site_root']);
redirect($options['site_root']);
?>