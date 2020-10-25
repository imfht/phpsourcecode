<?php

/**
 * 管理后台
 * 
 * @author ShuangYa
 * @package Blog
 * @category Controller
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

namespace blog\controller;
use \Sy;
use \sy\base\Controller;
use \sy\base\Router;
use \blog\libs\Common;
use \blog\model\Admin;
use \blog\model\Link as LinkModel;

class Link extends Controller {
	public function __construct() {
		if (!Admin::checkLogin()) {
			Admin::gotoLogin();
		}
		$this->assign('page', 'link');
	}
	/**
	 * 链接
	 */
	public function actionList() {
		if (isset($_POST['ajax'])) {
			Sy::setMimeType('json');
			$page = (isset($_POST['page']) && $_POST['page'] > 0) ? (intval($_POST['page'] - 1)) : 0;
			$start = $page * 30;
			$list = LinkModel::getList(['limit' => $start . ',30'])->getAll();
			$num = LinkModel::getNum();
			echo json_encode(['success' => 1, 'count' => $num, 'list' => $list]);
		} else {
			Sy::setMimeType('html');
			$this->display('link/list');
		}
	}
	/**
	 * Ajax删除链接
	 */
	public function actionDel() {
		Sy::setMimeType('json');
		LinkModel::del($_POST['id']);
		echo json_encode(['success' => 1]);
	}
	/**
	 * Ajax增加链接
	 */
	public function actionAdd() {
		Sy::setMimeType('json');
		LinkModel::add($_POST['title'], $_POST['rel'], $_POST['url']);
		echo json_encode(['success' => 1]);
	}
	/**
	 * Ajax修改链接
	 */
	public function actionEdit() {
		Sy::setMimeType('json');
		LinkModel::set($_POST['id'], $_POST['title'], $_POST['rel'], $_POST['url']);
		echo json_encode(['success' => 1]);
	}
}
