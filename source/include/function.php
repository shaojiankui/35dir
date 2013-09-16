<?php
/** 分页函数 */
function showpage($pageurl, $totalnum, $curpage, $perpage = 20) {
	$pagenav = '';
	$pageurl .= (strpos($pageurl, '?') === false) ? '?' : '&';
	
	if ($totalnum > 0) {
		$pagestep = 8;
		$offset = 5;
		$pagenum = @ceil($totalnum / $perpage);
		
		if ($pagestep > $pagenum) {
			$start = 1;
			$end = $pagenum;
		} else {			
			$start = $curpage - $offset;
			$end = $curpage + $pagestep - $offset - 1;	
			
			if ($start < 1) {
				$end = $curpage + 1 - $start;
				$start = 1;
				
				if (($end - $start) < $pagestep && ($end - $start) < $pagenum) {
					$end = $pagestep;
				}				
			} elseif ($end > $pagenum) {
				$start = $curpage - $pagenum + $end;
				$end = $pagenum;
				
				if (($end - $start) < $pagestep && ($end - $start) < $pagenum) {
					$start = $pagenum - $pagestep + 1;
				}
			}
		}

		$pagenav = ($curpage > 1 && $pagenum > $pagestep ? '<a href="'.$pageurl.'page=1" class="pages" title="首页">&laquo;</a>' : '').($curpage > 1 ? '<a href="'.$pageurl.'page='.($curpage - 1).'"  class="pages" title="上一页">&lsaquo;</a>' : '');
		
		for($i = $start; $i <= $end; $i++) {
			$pagenav .= $i == $curpage ? '<span class="current">'.$i.'</span>' : '<a href="'.$pageurl.'page='.$i.'" class="pages">'.$i.'</a>';
		}
		
		$pagenav .= ($curpage < $pagenum ? '<a href="'.$pageurl.'page='.($curpage + 1).'" class="next_page" title="下一页">&rsaquo;</a><a href="'.$pageurl.'page='.$pagenum.'" class="last_page" title="尾页">&raquo;</a>' : '');
		
		/*
		if ($pagenum > 30) {
			$pagenav .= '<span class="jump_page">转至第<input type="text" name="page" size="1" maxlength="5" value="'.$curpage.'" onKeyPress="if (event.keyCode==13) window.location=\''.$pageurl.'page=\'+this.value;">页</span>';
		}
		*/
		
		$pagenav = $pagenav ? '<span class="total_page">共 '.$totalnum.' 条</span>'.$pagenav : '';
	}
	
	return $pagenav;
}

function opt_checked($compare1, $compare2) {
    if ($compare1 == $compare2) {
		$checked = ' checked';
	} else {
		$checked = '';
	}
	
	return $checked;
}

function opt_selected($compare1, $compare2) {
    if ($compare1 == $compare2) {
		$selected = ' selected';
	} else {
		$selected = '';
	}
	
	return $selected;
}

function opt_display($compare1, $compare2) {
    if ($compare1 == $compare2) {
		$display = '';
	} else {
		$display = 'none';
	}
	
	return $display;
}

/** 计算UTF8字符串长度 */
function utf8_strlen($string = '') {
	preg_match_all("/./us", $string, $match);
	return count($match[0]);
}

/** 自动转义 */
function hf_magic_quotes() {
	if (get_magic_quotes_gpc()) {
		$_GET = stripslashes_deep($_GET);
		$_POST = stripslashes_deep($_POST);
		$_COOKIE = stripslashes_deep($_COOKIE);
	}
	
	$_GET = add_magic_quotes($_GET);
	$_POST = add_magic_quotes($_POST);
	$_COOKIE = add_magic_quotes($_COOKIE);
	$_SERVER = add_magic_quotes($_SERVER);

	$_REQUEST = array_merge($_GET, $_POST);
}

/** 去除转义字符 */
function stripslashes_deep($value) {
	if (is_array($value)) {
		$value = array_map('stripslashes_deep', $value);
	} elseif (is_object($value)) {
		$vars = get_object_vars($value);
		foreach ($vars as $key => $data) {
			$value->{$key} = stripslashes_deep($data);
		}
	} else {
		$value = stripslashes($value);
	}
	
	return $value;
}

/** 添加转义字符 */
function add_magic_quotes($array) {
	foreach ((array) $array as $k => $v) {
		if (is_array($v)) {
			$array[$k] = add_magic_quotes($v);
		} else {
			$array[$k] = addslashes($v);
		}
	}
	
	return $array;
}

/** 去除脚本字符 */
function stripscript($string) {
	$search = array("/<script.*>.*<\/script>/siU", '/on(mousewheel|mouseover|click|load|onload|submit|focus|blur)="[^"]*"/i');
	$replace = array('', '');
	$string = preg_replace($search, $replace, $string);
	
	return $string;
}

/** 计算时间隔 */
function datediff($format, $timestamp) {
	$newtime = time() - $timestamp;
	
	$hour = floor($newtime / 3600);
	$day = floor($newtime / (24 * 3600));
	$week = floor($newtime / (7 * 24 * 3600));
	$month = floor($newtime / (30 * 24 * 3600));

	$format = strtolower($format);
	switch ($format) {
		case 'h' :
			return $hour;
			break;
		case 'd' :
			return $day;
			break;
		case 'w' :
			return $week;
			break;
		case 'm' :
			return $month;
			break;
	}
}

/** is8601时间 */
function iso8601($format, $timestamp = NULL) {
	if ($timestamp === NULL) {
		$timestamp = time() - date('Z');
	} elseif ($timestamp <= 0) {
		return '';
	}
	$timestamp += (8 * 3600);
	
	return gmdate($format, time());
}

/** 表单HASH */
function get_formhash() {
	$formhash = substr(md5(substr(time(), 0, -7)), 8, 8);
	
	return $formhash;
}

/** 生成指定长度的随机字符串 */
function random($length = 16, $isnum = false){
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $isnum ? 10 : 35);
	$seed = $isnum ? $seed.'zZ'.strtoupper($seed) : str_replace('0', '', $seed).'01234056789';
	
	$randstr = '';
	$max = strlen($seed) - 1;
	for ($i = 0; $i < $length; $i++) {
		$randstr .= $seed{mt_rand(0, $max)};
	}
	return $randstr;
}

/** 编码函数 */
function authcode($string, $operation = 'ENCODE', $key = '', $expiry = 0) {
	$ckey_length = 4;

	$key = md5($key ? $key : 'yeN3g9EbNfiaYfodV63dI1j8Fbk5HaL7W4yaW4y7u2j4Mf45mfg2v899g451k576');
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

/** 将数组转换为以逗号分隔的字符串 */
function dimplode($array) {
	if (!empty($array)) {
		return "'".implode("','", is_array($array) ? $array : array($array))."'";
	} else {
		return '';
	}
}

/** apache模块检测 */
function apache_mod_enabled($module) {
	if (function_exists('apache_get_modules')) {
		$apache_mod = apache_get_modules();
		if (in_array($module, $apache_mod)) {
			return true;
		} else {
			return false;
		}
	}
}

/** 获取客户端IP */
function get_client_ip() {
	if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$client_ip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$client_ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$client_ip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$client_ip = $_SERVER['REMOTE_ADDR'];
	}
	
	$client_ip = addslashes($client_ip);
	@preg_match("/[\d\.]{7,15}/", $client_ip, $ip);
	$ip_addr = $ip[0] ? $ip[0] : 'unknown';
	unset($ip);
	
	return $ip_addr;
}

function get_domain($url) {
	if (preg_match("/^(http:\/\/)?([^\/]+)/i", $url, $domain)) {
		return $domain[2];
	} else {
		return false;
	}
}

function format_url($url) {
	if ($url != "") {
		$url_parts = parse_url($url);
		$scheme = $url_parts['scheme'];
		$host = $url_parts['host'];
		$path = $url_parts['path'];
		$port = !empty($url_parts['port']) ? ':'.$url_parts['port'] : '';
		$url = (!empty($scheme) ? $scheme.'://'.$host : (!empty($host) ? 'http://'.$host : 'http://'.$path)).$port.'/';
		
		return $url;
	}
}

/** 获取指定URL内容 */
function get_url_content($url) {
	if (empty($url)) {
    	return false;
	}
	
	if (substr($url, 0, 7) != 'http://') {
		$url = 'http://'.$url;
	}
	
	$timeout = 30;
    $data = '';
    for ($i = 0; $i < 5 && empty($data); $i++) {
		if (function_exists('curl_init')) {
			$ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
			
        	$data = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($http_code != '200') {
				return false;
			}
        } elseif (function_exists('fsockopen')) {
			$params = parse_url($url);
			$host = $params['host'];
			$path = $params['path'];
			$query = $params['query'];
			$fp = @fsockopen($host, 80, $errno, $errstr, $timeout);
			if (!$fp) {
				return false;
			} else {
				$result = '';
				$out = "GET /" . $path . '?' . $query . " HTTP/1.0\r\n";
				$out .= "Host: $host\r\n";
				$out .= "Connection: Close\r\n\r\n";
				@fwrite($fp, $out);
				$http_200 = preg_match('/HTTP.*200/', @fgets($fp, 1024));
				if (!$http_200) {
					return false;
				}

				while (!@feof($fp)) {
                if ($get_info) {
                    $data .= @fread($fp, 1024);
                } else {
                    if (@fgets($fp, 1024) == "\r\n") {
                        $get_info = true;
                    }
                }
            }
            @fclose($fp);
        }
        } elseif (function_exists( 'file_get_contents')) {
			if (!get_cfg_var('allow_url_fopen')) {
				return false;
			}
            $context = stream_context_create(
				array('http' => array('timeout' => $timeout))
			);
            $data = @file_get_contents($url, false, $context);
        } else {
			return false; 
		}
	}
	
	if (!$data) {
		return false;
    } else {
		$encode = mb_detect_encoding($data, array('ascii', 'gb2312', 'utf-8', 'gbk'));
		if ($encode == 'EUC-CN' || $encode == 'CP936') {
			$data = @mb_convert_encoding($data, 'utf-8', 'gb2312');
		}
		
        return $data;
	}
}

/** 检查非法关键词 */
function censor_words($keywords = '', $content = '') {
	$checked = true;
	if (!empty($keywords) && !empty($content)) {
		$wordarr = explode(',', $keywords);
		foreach ($wordarr as $val) {
			if (preg_match('/'.$val.'/i', $content)) {
				$checked = false;
			}
		}
	}
	
	return $checked;
}

/** 获取内容中的链接 */
function get_content_links($document) {	
	preg_match_all("'<\s*a\s.*?href\s*=\s*([\"\'])?(?(1) (.*?)\\1 | ([^\s\>]+))'isx", $document, $matches);

	while(list($key, $val) = each($matches[2])) {
		if (!empty($val)) $links[] = $val;
	}
		
	while(list($key, $val) = each($matches[3])) {
		if (!empty($val)) $links[] = $val;
	}
	
	return $links;
}

/** 保存远程文件到本地 */
function save_to_local($weburl, $savepath = '') {
	$succeed = false;
	
	set_time_limit(0);
	if (substr($savepath, -1) != '/') $savepath .= '/';
	if (!is_dir($savepath)) @mkdir($savepath, 0777);
	
	$imgurl = 'http://open.thumbshots.org/image.pxf?url='.$weburl;
	$newpath = $savepath.$weburl.'.jpg';
	$data = get_url_content($imgurl);
	if (strlen($data) != 1984) {
		if ($data) {
			$fp = @fopen($newpath, "w");
       		@fwrite($fp, $data);
       		@fclose($fp);
			
			$succeed = true;
		}
	}
	
	if ($succeed) {
		return $newpath;
	} else {
		return $succeed;
	}
}
?>