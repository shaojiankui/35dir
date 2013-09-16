<?php
function is_valid_dir($str) {
	if (preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $str)) {
		return true;
	} else {
		return false;
	}
}

function is_valid_url($url) {
	if (preg_match('/^http(s)?:\/\//i', $url)) {
		return true;
	} else {
		return false;
	}
}

function is_valid_email($email) {
	if (preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $email)) {
		return true;
	} else {
		return false;
	}
}

function is_valid_domain($domain) {
	if (preg_match("/^([0-9a-z-]{1,}.)?[0-9a-z-]{2,}.([0-9a-z-]{2,}.)?[a-z]{2,}$/i", $domain)) {
		return true;
	} else {
		return false;
	}
}
?>