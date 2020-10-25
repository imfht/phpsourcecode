<?php
namespace YesfApp\Http;

class FakeRequest {
	public $header;
	public $server;
	public $get;
	public $post;
	public $cookie;
	public $files;
	public $raw_content;
	public function __construct() {
		$this->header = [
			'accept' => 'text/html;q=0.9,image/webp,image/apng,*/*;q=0.8',
			'referer' => 'http://example.com'
		];
		$this->server = [
			'request_time' => time()
		];
		$this->get = [
			'action' => 'test'
		];
		$this->cookie = [
			'user' => 'root'
		];
	}
	public function rawContent() {
		return $this->raw_content;
	}
}