<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class NewAction extends BaseAction{
	
    public function _initialize() {
        parent::_initialize();
				// 标签列表
        $Tag = M("Tag");
        $list  = $Tag->where("module='New'")->field('id,name,count')->order('count desc')->limit('0,25')->select();
        $this->assign('tags',$list);
    }

    // 新闻首页
    public function index() {
        $this->_list("New","status=1 AND newtype='".intval($_GET["newtype"])."'");
        $this->display();
    }

}
?>