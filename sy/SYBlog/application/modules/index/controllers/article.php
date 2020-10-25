<?php

/**
 * 文章
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
use \sy\lib\YHtml;
use \blog\libs\Common;
use \blog\model\Article as ArticleModel;
use \blog\model\Meta;

class Article extends Controller {
	public function __construct() {
	}
	/**
	 * 列表
	 */
	public function actionList() {
		$type = $_GET['type'];
		if (!in_array($type, ['id', 'tag', 'all'], TRUE)) {
			header(Sy::getHttpStatus(404));
			exit;
		}
		$pagesize = intval(Common::option('pagesize'));
		$page = (isset($_GET['page']) && intval($_GET['page']) >= 0) ? (intval($_GET['page']) - 1) : 0;
		$start = $page * $pagesize;
		if ($type === 'id') { //按ID查找
			$id = intval($_GET['val']);
			$list = ArticleModel::getList(['body' => TRUE, 'find' => 'id', 'id' => $id, 'limit' => $start . ',' . $pagesize]);
			$title = Meta::get($id);
			$title = $title['title'];
		} elseif ($type === 'tag') { //按Tag
			$tag = $_GET['val'];
			$list = ArticleModel::getList(['body' => TRUE, 'find' => 'tag', 'tag' => $tag, 'limit' => $start . ',' . $pagesize]);
			$title = $tag;
		} else { //不筛选
			$list = ArticleModel::getList(['body' => TRUE, 'find' => NULL, 'limit' => $start . ',' . $pagesize]);
			$title = '所有文章';
		}
		$list->setPage($page + 1);
		Sy::setMimeType('html');
		$this->assign('list', $list);
		$this->assign('tid', $id);
		$this->assign('title', $title);
		$this->display('article/list');
	}
	/**
	 * 查看文章
	 */
	public function actionView() {
		$id = $_GET['id'];
		if (!is_numeric($id)) {
			header(Sy::getHttpStatus(404));
			exit;
		}
		$article = ArticleModel::get($id);
		$title = $article->title;
		if (empty($title)) {
			header(Sy::getHttpStatus(404));
			exit;
		}
		Sy::setMimeType('html');
		$this->assign('article', $article);
		$this->assign('title', $article->title);
		$this->display('article/view');
	}
}