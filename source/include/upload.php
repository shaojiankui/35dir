<?php
class upload_file {
	public $attach = array();
	public $allow_type = array('bmp', 'gif', 'jpg', 'png');
	public $allow_size = 10485760;
	public $error_code = 0;
	
	public function init($attach, $savepath) {
		if (!is_array($attach) || empty($attach) || !$this->is_upload_file($attach['tmp_name']) || trim($attach['name']) == '' || $attach['size'] == 0) {
			$this->attach = array();
			$this->error_code = -101;
			return false;
		} else {
			$attach['name'] = trim($attach['name']);
			$attach['size'] = intval($attach['size']);
			$attach['ext'] = $this->get_ext($attach['name']);
			$attach['path'] = $savepath.$this->make_filename($attach['name']).'.'.$attach['ext'];
			
			$this->attach = & $attach;
			$this->error_code = 0;
		}
	}
	
	public function save_file() {
		if (empty($this->attach) || empty($this->attach['tmp_name']) || empty($this->attach['path'])) {
			$this->error_code = -101;
			return false;
		} elseif ($this->check_filetype($this->attach['ext'])) {
			$this->error_code = -102;
			return false;
		} elseif (!$this->check_filesize($this->attach['size'])) {
			$this->error_code = -103;
			return false;
		} elseif (!$this->save_to_local($this->attach['tmp_name'], $this->attach['path'])) {
			$this->error_code = -104;
			return false;
		} else {
			$this->error_code = 0;
			return true;
		}
	}
	
	public function save_to_local($source, $target) {
		if (!$this->is_upload_file($source)) {
			$succeed = false;
		} elseif (function_exists('move_uploaded_file') && @move_uploaded_file($source, $target)) {
			$succeed = true;
		} elseif (@copy($source, $target)) {
			$succeed = true;
		} elseif (@is_readable($source) && (@$fp_s = fopen($source, 'rb')) && (@$fp_t = fopen($target, 'wb'))) {
			while (!feof($fp_s)) {
				$s_data = @fread($fp_s, @filesize($source));
				@fwrite($fp_t, $s_data);
			}
			fclose($fp_s);
			fclose($fp_t);
			$succeed = true;
		}

		if ($succeed) {
			$this->error_code = 0;
			@chmod($target, 0644);
			@unlink($source);
		}

		return $succeed;
	}
	
	private function is_upload_file($source) {
		return $source && ($source != 'none') && (is_uploaded_file($source) || is_uploaded_file(str_replace('\\\\', '\\', $source)));
	}
	
	private function check_filetype($type) {
		return $type && in_array($type, $this->allow_type) ? 0 : 1;
	}
	
	private function check_filesize($size) {
		return $size && $size > $this->allow_size ? 0 : 1;
	}
	
	private function get_ext($filename) {
		return addslashes(strtolower(substr(strrchr($filename, '.'), 1, 10)));
	}
	
	private function make_filename() {
		return md5(uniqid(rand(), true));
	}
	
	public function make_dir($dir, $index = true) {
		$res = true;
		if (!is_dir($dir)) {
			$res = @mkdir($dir, 0777);
			$index && @touch($dir.'/index.html');
		}
		return $res;
	}
	
	public function error() {
		$errstr = '';
		switch ($this->error_code) {
			case -101 :
				$errstr = '上传文件不存在或不合法！';
				break;
			case -102 :
				$errstr = '不允许上传的文件类型！';
				break;
			case -103 :
				$errstr = '上传文件大小超出限制！';
				break;
			case -104 :
				$errstr = '无法写入文件或写入失败！';
				break;
		}
		return $errstr;
	}
}
?>