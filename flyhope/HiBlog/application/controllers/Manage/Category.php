<?php
/**
 * 分类管理
 *
 * @author chengxuan <i@chengxuan.li>
 */
class Manage_CategoryController extends AbsController {

    public function indexAction() {
        $categorys = Model\Category::showUserAll();
        $this->viewDisplay(['categorys' => $categorys]);
    }
}