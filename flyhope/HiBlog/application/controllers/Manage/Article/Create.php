<?php
/**
 * 文章管理-发表文章页面
 *
 * @author chengxuan <i@chengxuan.li>
 */
class Manage_Article_CreateController extends AbsController {

    public function indexAction() {
        $categorys = Model\Category::showUserAll();
        $this->viewDisplay(array(
            'form_action'  => Comm\View::path('aj/manage/article/create'),
            'categorys'    => $categorys,
        ), 'manage/article/update');
    }
}