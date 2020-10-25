<?php
// +----------------------------------------------------------------------
// |   精灵后台系统 [ 基于TP5，快速开发web系统后台的解决方案 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 - 2017 http://www.apijingling.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wapai 邮箱:wapai@foxmail.com
// +---------------------------------------------------------------------- 

namespace app\admin\controller;  
/**
 * 后台首页控制器
 * @author wapai   邮箱:wapai@foxmail.com
 */
class Index extends Admin  {

    /**
     * 后台首页
     * @author wapai   邮箱:wapai@foxmail.com
     */
    public function index(){ 
        $this->assign('meta_title','管理首页') ;
        return $this->fetch();
    }

}
