<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class CaseAction extends BaseAction {

    public function _initialize() {
        parent::_initialize();
				// 标签列表
        $Tag = M("Tag");
        $list  = $Tag->where("module='Case'")->field('id,name,count')->order('count desc')->limit('0,25')->select();
        $this->assign('tags',$list);
    }
    // 案例展示页
    public function index()
    {
        $this->_list('Case','status=1');
        $this->display();
    }
}
?>