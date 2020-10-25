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
use \blog\model\Article as ArticleModel;

class Article extends Controller {
	public function __construct() {
		if (!Admin::checkLogin()) {
			Admin::gotoLogin();
		}
		$this->assign('page', 'content');
	}
	/**
	 * 文章
	 */
	public function actionList() {
		if (isset($_POST['ajax'])) {
			Sy::setMimeType('json');
			$page = (isset($_POST['page']) && $_POST['page'] > 0) ? (intval($_POST['page'] - 1)) : 0;
			$start = $page * 30;
			$list = ArticleModel::getList(['body' => FALSE, 'limit' => $start . ',30'])->getAll();
			$num = ArticleModel::getNum();
			echo json_encode(['success' => 1, 'count' => $num, 'list' => $list]);
		} else {
			Sy::setMimeType('html');
			$this->display('article/list');
		}
	}
	/**
	 * 修改文章
	 */
	public function actionEdit() {
		Sy::setMimeType('json');
		if (empty($_POST['title'])) {
			echo json_encode(['success' => 0, 'message' => i18n::get('title_can_not_be_empty')]);
			exit;
		}
		$time = strtotime($_POST['time']);
		ArticleModel::set($_POST['id'], $_POST['title'], $_POST['tags'], $time);
		echo json_encode(['success' => 1, 'time' => $time]);
	}
	/**
	 * 删除文章
	 */
	public function actionDel() {
		Sy::setMimeType('json');
		ArticleModel::del($_POST['id']);
		echo json_encode(['success' => 1]);
	}
	/**
	 * 编辑文章
	 */
	public function actionWrite() {
		if (isset($_POST['title'])) {
			Sy::setMimeType('json');
			if (empty($_POST['title'])) {
				echo json_encode(['success' => 0, 'message' => i18n::get('title_can_not_be_empty')]);
				exit;
			}
			if (isset($_POST['id'])) {
				ArticleModel::set($_POST['id'], $_POST['title'], $_POST['tags'], strtotime($_POST['time']), $_POST['body']);
			} else {
				ArticleModel::add($_POST['title'], $_POST['tags'], strtotime($_POST['time']), $_POST['body']);
			}
			echo json_encode(['success' => 1]);
		} else {
			Sy::setMimeType('html');
			if (isset($_GET['id']) && !empty($_GET['id'])) {
				$id = intval($_GET['id']);
				$article = ArticleModel::get($id);
				$data = ['title' => '编辑文章', 'page' => 'ArticleEdit', 'id' => $id, 'article' => $article];
			} else {
				$data = ['title' => '撰写文章', 'page' => 'ArticleNew', 'id' => $id, 'article' => $article];
			}
			foreach ($data as $k => $v) {
				$this->assign($k, $v);
			}
			$this->assign('css', ['@root/assets/css/editor.css']);
			$this->assign('js', ['@root/assets/js/editor.full.js']);
			$this->display('article/write');
		}
	}
}
