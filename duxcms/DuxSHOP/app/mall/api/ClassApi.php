<?php

/**
 * 商品分类
 */

namespace app\mall\api;

use \app\base\api\BaseApi;

class ClassApi extends BaseApi {

    protected $_middle = 'mall/Category';

    /**
     * 列表
     */
    public function index() {

        target($this->_middle, 'middle')->treeList()->export(function ($data) {
            $this->success('ok', $data['treeList']);
        });

    }

}