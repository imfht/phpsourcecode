<?php
/**
 * 发布管理
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Manage_Publish_IndexController extends AbsController {

    public function indexAction() {
        $categorys = Model\Category::showUserAll();
        $this->viewDisplay(['categorys' => $categorys]);
    }
}