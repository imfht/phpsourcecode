<?php

/**
 * 基本信息设置页
 *
 * @package Controller
 * @author  chengxuan <i@chengxuan.li>
 */
class Manage_BasicController extends AbsController {

    public function indexAction() {
        $blog = Model\Blog::show();
        $basic = new \Entity\Tarr(isset($blog['data']) ? $blog['data'] : array());
        $this->viewDisplay(['basic' => $basic]);
    }
} 