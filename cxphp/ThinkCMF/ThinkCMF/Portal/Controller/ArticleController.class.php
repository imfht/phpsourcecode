<?php

/**
 * 文章内页
 */

namespace Portal\Controller;

use Common\Controller\HomeBaseController;

class ArticleController extends HomeBaseController {

	//文章内页
	public function index() {
		$article = sp_sql_post(I('get.id'), '');
		$termid = $article['term_id'];
		$term_obj = new \Admin\Model\TermsModel();
		$term = $term_obj->where("term_id='$termid'")->find();
		$smeta = json_decode($article[smeta], true);
		$this->assign($article);
		$this->assign("smeta", $smeta);
		$this->assign("term", $term);
		$tplname = empty($term["one_tpl"]) ? "article" : $term["one_tpl"];
		$this->display("Portal:$tplname");
	}

}
