<?php
    $menus = array();

    if ($this->menupanel[0] == 'book') {
        $menus = array(
            array('label'=>'小说栏目管理', 'url'=> $this->createUrl('category/index'), 'active'=> $this->id == 'category' ? true : false),
            array('label'=>'小说管理', 'url'=> $this->createUrl('book/index'),  'active'=> $this->id == 'book' ? true : false),
            array('label'=>'小说章节管理', 'url'=> $this->createUrl('article/index'),  'active'=> $this->id == 'article' ? true : false),
        );
    } elseif ($this->menupanel[0] == 'user') {
        $menus = array(
            array('label'=>'会员管理', 'url'=> $this->createUrl('user/index'), 'active'=> $this->id == 'user' ? true : false),
            array('label'=>'管理员管理', 'url'=> $this->createUrl('adminuser/index'), 'active'=> $this->id == 'adminuser' ? true : false),
        );
    } elseif ($this->menupanel[0] == 'system') {
        $menus = array(
            array('label'=>'系统信息', 'url'=> $this->createUrl('system/index'), 'active'=> $this->action->id == 'index' ? true : false),
            array('label'=>'基础属性', 'url'=> $this->createUrl('system/base'), 'active'=> $this->action->id == 'base' ? true : false),
            array('label'=>'伪静态设置', 'url'=> $this->createUrl('system/rewrite'), 'active'=> $this->action->id == 'rewrite' ? true : false),
            array('label'=>'缓存管理', 'url'=> $this->createUrl('system/cache'), 'active'=> $this->action->id == 'cache' ? true : false),
        );
    } elseif ($this->menupanel[0] == 'friendlink') {
        $menus = array(
            array('label'=>'友链列表', 'url'=> $this->createUrl('friendlink/index'), 'active'=> $this->id == 'friendlink' ? true : false),
//            array('label'=>'伪静态设置', 'url'=> $this->createUrl('system/rewrite'), 'active'=> $this->action->id == 'rewrite' ? true : false),
        );
    } elseif ($this->menupanel[0] == 'news') {
        $menus = array(
            array('label'=>'新闻分类', 'url'=> $this->createUrl('newscategory/index'), 'active'=> $this->id == 'newscategory' ? true : false),
            array('label'=>'新闻管理', 'url'=> $this->createUrl('news/index'), 'active'=> $this->id == 'news' ? true : false),
//            array('label'=>'伪静态设置', 'url'=> $this->createUrl('system/rewrite'), 'active'=> $this->action->id == 'rewrite' ? true : false),
        );
    } elseif ($this->menupanel[0] == 'ads') {
        $menus = array(
            array('label'=>'广告管理', 'url'=> $this->createUrl('ads/index'), 'active'=> $this->id == 'ads' ? true : false),
//            array('label'=>'新闻管理', 'url'=> $this->createUrl('news/index'), 'active'=> $this->id == 'news' ? true : false),
    //            array('label'=>'伪静态设置', 'url'=> $this->createUrl('system/rewrite'), 'active'=> $this->action->id == 'rewrite' ? true : false),
        );
    } elseif ($this->menupanel[0] == 'modules') {
        $menus = array(
            array('label'=>'模块管理', 'url'=> $this->createUrl('modules/index'), 'active'=> $this->id == 'modules' ? true : false),
//            array('label'=>'新闻管理', 'url'=> $this->createUrl('news/index'), 'active'=> $this->id == 'news' ? true : false),
            //            array('label'=>'伪静态设置', 'url'=> $this->createUrl('system/rewrite'), 'active'=> $this->action->id == 'rewrite' ? true : false),
        );
    } else {
        $menus = array(
            array('label'=>'小说', 'url'=> $this->createUrl('site/index'), 'active'=> $this->id == 'site' ? true : false),
        );
    }
    $this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'pills', // '', 'tabs', 'pills' (or 'list')
    'stacked'=> true, // whether this is a stacked menu
    'htmlOptions' => array (
        'class' => 'nav-tabs',
    ),
    'items'=> $menus,
)); ?>