<?php

/**
 * API Controller基本类
 * 
 * @author ShuangYa
 * @package Blog
 * @category Library
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

namespace blog\libs;
use \Sy;

class ApiController extends \sy\base\Controller {
	public function __construct() {
		Sy::setMimeType('json');
		//检查签名
		$this->checkSign();
	}

	public function error($msg) {
		echo json_encode(['success' => 0, 'message' => $msg]);
		exit;
	}
	public function success($data) {
		echo json_encode(['success' => 1, 'data' => $data]);
		exit;
	}

	public function checkSign() {
		$data = file_get_contents('php://input');
		$key = Common::option('apiKey');
		$sign = hash_hmac('sha256', $data, $key);
		if ($sign !== $_SERVER['HTTP_X_SIGN']) {
			// $this->error('签名校验失败，预期' . $sign . '，实际' . $_SERVER['HTTP_X_SIGN']);
			$this->error('签名校验失败');
		}
		$_POST = json_decode($data, 1);
	}
}