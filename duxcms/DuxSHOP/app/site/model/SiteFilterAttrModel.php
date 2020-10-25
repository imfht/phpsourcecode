<?php

/**
 * 筛选属性
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteFilterAttrModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'attr_id',
        'validate' => [
            'name' => [
                'len' => ['1, 50', '名称输入不正确!', 'must', 'all'],
            ],
        ],
        'format' => [
            'name' => [
                'function' => ['html_in', 'all'],
            ],
        ],
        'into' => '',
        'out' => '',
    ];


    public function loadList($where = [], $limit = 0, $order = '') {
        return parent::loadList($where, $limit, 'attr_id asc');
    }


}