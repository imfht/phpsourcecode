<?php
/**
 * 发布文章
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Publish_ArticleController extends Aj_AbsController {


    public function indexAction() {
        $id = Comm\Arg::post('id', FILTER_VALIDATE_INT);

        $article = Model\Article::show($id);
        if(!$article) {
            throw new Exception\Msg('发布文章为空');
        }
        
        $result = Model\Publish::article($article);
        Comm\Response::json(100000, 'succ', ['result' => $result], false);
    }

}
