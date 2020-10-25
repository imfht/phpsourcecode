<?php
/**
 * 编辑文章
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Article_editController extends Aj_AbsController {

    public function indexAction() {
        $id = Comm\Arg::post('id');
        $category_id = Comm\Arg::post('category_id');
        $title = Comm\Arg::post('title');
        $content = Comm\Arg::post('content');

        Model\Article::edit($id, $category_id, $title, $content);

        Comm\Response::json(100000, '发表成功', ['href' => Comm\View::path('manage/article/index')], false);
    }

}
