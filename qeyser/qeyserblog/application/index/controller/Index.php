<?php
namespace app\index\controller;
use app\index\controller\Base;

/**.-------------------------------------------------------------------
 * |    Software: [QeyserBlog]
 * |    Site: www.qeyser.net
 * |-------------------------------------------------------------------
 * |    Author: 凯撒 <125790757@qq.com>
 * |    WeChat: 15999230034
 * |    Copyright (c) 2017-2018, www.qeyser.net . All Rights Reserved.
 * '-------------------------------------------------------------------*/

class Index extends Base{
    /**
     * 首页控制器
     */
    public function index(){
    	// 文章列表
    	$datalist=db('article')->order('time desc,sort desc')->paginate(Config('index_page'));
    	$this->assign('datalist',$datalist);
        // 显示tag列表
        $this->tags();
        // 显示友情链接
        $this->freandlink();
        // 显示模板
        return $this->fetch();
    }
}
