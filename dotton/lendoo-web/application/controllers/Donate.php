<?php
require_once __DIR__ . '/AdminController.php';

use \LeanCloud\Object;
use \LeanCloud\Query;
use \LeanCloud\File;

class Donate extends AdminController {
	public function index() {
		$query = new Query("Donate");
		$query->equalTo('status', true);
		$query->_include('user');
		$query->descend("updatedAt");
		$result = $query->find();
		// 渲染
		$data['title'] = '赞赏列表';
		$data['result'] = $result;
		$this->layout->view('donate/index', $data);
	}
}