<?php
namespace YesfApp\Http;

class FakeResponse {
	public $headers = [];
	public $content = '';
	public $status = 200;
	public function status($code) {
		$this->status = $code;
	}
	public function header($k, $v) {
		$this->headers[$k] = $v;
	}
	public function write($str) {
		$this->content .= $str;
	}
	public function cookie($name, $value, $expire, $path, $domain, $https, $httponly) {
		// Do nothing
	}
	public function sendfile($file, $offset, $length) {
		$this->content = file_get_contents($file, false, null, $offset, $length);
	}
	public function end() {
		// Do nothing
	}
}