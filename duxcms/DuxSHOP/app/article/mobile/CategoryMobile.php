<?php

/**
 * 商城分类
 */

namespace app\article\mobile;

class CategoryMobile extends \app\base\mobile\SiteMobile {


    protected $_middle = 'article/Category';

    /**
     * 首页
     */
    public function index() {
        target($this->_middle, 'middle')->meta()->treeList()->export(function ($data) {
            $this->assign($data);
            $this->mobileDisplay();
        });
    }

}