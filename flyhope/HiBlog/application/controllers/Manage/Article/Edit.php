<?php
/**
 * 编辑页
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Manage_Article_EditController extends AbsController {

    public function indexAction() {
        $id = Comm\Arg::get('id', FILTER_VALIDATE_INT, ['min_range' => 1], true);
        
        $article = Model\Article::show($id);
        if(empty($article)) {
            throw new Exception\Msg(_('文章不存在'));
        }
        Model\User::validateAuth($article['uid']);
        
        $categorys = Model\Category::showUserAll();
        
        $this->viewDisplay(array(
            'article'      => $article,
            'form_action'  => Comm\View::path('aj/manage/article/edit'),
            'categorys'    => $categorys,
        ), 'manage/article/update');
    }
}