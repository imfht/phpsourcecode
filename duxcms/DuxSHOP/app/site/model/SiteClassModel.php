<?php

/**
 * 基础栏目
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteClassModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'category_id',
        'validate' => [
            'name' => [
                'required' => ['', '分类名不能为空!', 'must', 'all'],
            ],
        ],
        'format' => [
            'name' => [
                'function' => ['htmlspecialchars', 'all'],
            ],
            'subname' => [
                'function' => ['htmlspecialchars', 'all'],
            ],
            'keyword' => [
                'function' => ['htmlspecialchars', 'all'],
            ],
            'description' => [
                'function' => ['htmlspecialchars', 'all'],
            ],
            'sort' => [
                'function' => ['intval', 'all'],
            ],
        ]
    ];

}