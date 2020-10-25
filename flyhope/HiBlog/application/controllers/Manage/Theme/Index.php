<?php
/**
 * 模板列表
 *
 * @package Controller
 * @author  chengxuan <i@chengxuan.li>
 */
class Manage_Theme_IndexController extends AbsController {
    
    
    public function indexAction() {
        //获取用户设置
        $blog = Model\Blog::show();
        $use_theme_id = isset($blog['data']['theme_id']) ? $blog['data']['theme_id'] : 0;
        
        //获取主题列表
        $themes = Model\Theme\Main::userTpls();
        $this->viewDisplay(array(
            'themes'       => $themes,
            'use_theme_id' => $use_theme_id,
        ));
    }
    
}
