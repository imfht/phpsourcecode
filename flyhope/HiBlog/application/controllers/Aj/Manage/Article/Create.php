<?php
/**
 * 创建文章
 *
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Article_createController extends Aj_AbsController {

	public function indexAction() {
	    $category_id = Comm\Arg::post('category_id');
		$title = Comm\Arg::post('title');
		$content = Comm\Arg::post('content');
		
		Model\Article::create($category_id, $title, $content);
		
		Comm\Response::json(100000, '发表成功', ['href' => Comm\View::path('manage/article/index')], false);
	}
	
}
