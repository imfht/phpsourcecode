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
use \blog\model\Meta as MetaModel;

class Meta extends Controller {
	public function __construct() {
		if (!Admin::checkLogin()) {
			Admin::gotoLogin();
		}
		$this->assign('page', 'content');
	}
	/**
	 * 标签
	 */
	public function actionList() {
		if (isset($_POST['ajax'])) {
			Sy::setMimeType('json');
			$page = (isset($_POST['page']) && $_POST['page'] > 0) ? (intval($_POST['page'] - 1)) : 0;
			$start = $page * 30;
			$num = MetaModel::getNum();
			$list = MetaModel::getList(['limit' => $start . ',30'])->getAll();
			echo json_encode(['success' => 1, 'count' => $num, 'list' => $list]);
		} else {
			Sy::setMimeType('html');
			$this->display('meta/list');
		}
	}
	/**
	 * Ajax修改标签
	 */
	public function actionEdit() {
		Sy::setMimeType('json');
		if (empty($_POST['title'])) {
			echo json_encode(['success' => 0, 'message' => i18n::get('title_can_not_be_empty')]);
			exit;
		}
		MetaModel::set($_POST['id'], $_POST['title'], $_POST['type']);
		echo json_encode(['success' => 1]);
	}
	/**
	 * Ajax查找标签
	 */
	public function actionGet() {
		Sy::setMimeType('json');
		if (empty($_POST['title'])) {
			echo json_encode(['success' => 1, 'list' => []]);
			exit;
		}
		$list = MetaModel::getList(['title' => $_POST['title'], 'limit' => '0,5'])->getAll();
		echo json_encode(['success' => 1, 'list' => $list]);
	}
}
