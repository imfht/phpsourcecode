<?php
class CurlClass {
	private $timeout = 5;

	function getUrlContents($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);
		return $file_contents;
	}
	function setTimeOut($timeOut){
		$this->timeout=$timeOut;
	}
}
?>