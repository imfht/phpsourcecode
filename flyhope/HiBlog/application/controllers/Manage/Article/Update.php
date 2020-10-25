<?php
/**
 * 文章管理-编辑文章页面
 *
 * @author chengxuan <i@chengxuan.li>
 */
class Manage_Article_UpdateController extends AbsController {

    public function indexAction() {
        $categorys = Model\Category::showUserAll();
        $this->viewDisplay(array(
            'categorys' => $categorys,
        ), 'manage/article/update');
    }
}