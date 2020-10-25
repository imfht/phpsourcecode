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
use \sy\lib\YHtml;
use \blog\libs\Common;
use \blog\libs\ApiController;
use \blog\model\Article as ArticleModel;
use \blog\model\Meta;

class Article extends ApiController {
	/**
	 * 列表
	 */
	public function actionList() {
		$type = isset($_POST['type']) ? $_POST['type'] : 'all';
		if (!in_array($type, ['id', 'tag', 'all'], TRUE)) {
			$this->error('类型无效');
		}
		$pagesize = intval(Common::option('pagesize'));
		$page = (isset($_POST['page']) && intval($_POST['page']) > 0) ? (intval($_POST['page']) - 1) : 0;
		$start = $page * $pagesize;
		$result = [];
		if ($type === 'id') { //按ID查找
			$id = intval($_POST['val']);
			$list = ArticleModel::getList(['body' => FALSE, 'find' => 'id', 'id' => $id, 'limit' => $start . ',' . $pagesize]);
			$result['tag'] = Meta::get($id);
			$result['title'] = $result['tag']['title'];
		} elseif ($type === 'tag') { //按Tag
			$tag = $_POST['val'];
			$list = ArticleModel::getList(['body' => FALSE, 'find' => 'tag', 'tag' => $tag, 'limit' => $start . ',' . $pagesize]);
			$result['title'] = $tag;
		} else { //不筛选
			$list = ArticleModel::getList(['body' => FALSE, 'find' => NULL, 'limit' => $start . ',' . $pagesize]);
			$result['title'] = '所有文章';
		}
		$result['page'] = $page + 1;
		$result['pagesize'] = $pagesize;
		$result['num'] = $list->num;
		$result['list'] = $list->getAll();
		$this->success($result);
	}
	/**
	 * 查看文章
	 */
	public function actionGet() {
		$id = $_POST['id'];
		if (!is_numeric($id)) {
			$this->error('文章不存在');
		}
		$article = ArticleModel::get($id);
		$title = $article->title;
		if (empty($title)) {
			$this->error('文章不存在');
		}
		$this->success([
			'id' => $id,
			'title' => $title,
			'tags' => $article->tags,
			'publish' => $article->publish,
			'body' => $article->body
		]);
	}
}