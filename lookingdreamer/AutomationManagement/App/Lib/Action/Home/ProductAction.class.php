<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class ProductAction extends BaseAction {

    public function _initialize() {
        parent::_initialize();
				// 标签列表
        $Tag = M("Tag");
        $list  = $Tag->where("module='Product'")->field('id,name,count')->order('count desc')->limit('0,25')->select();
        $this->assign('tags',$list);
    }
    
    public function index()
    {
        $this->_list('Product','status=1');
        $this->display();
    }
}
?>