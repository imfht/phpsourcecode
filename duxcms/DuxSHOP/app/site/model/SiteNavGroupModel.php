<?php

/**
 * 导航管理
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteNavGroupModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'group_id',
        'validate' => [
            'name' => [
                'len' => ['1, 20', '分组名称输入不正确!', 'must', 'all'],
            ],
        ],
        'format' => [
            'name' => [
                'function' => ['htmlspecialchars', 'all'],
            ],
            'description' => [
                'function' => ['htmlspecialchars', 'all'],
            ],
        ],
        'into' => '',
        'out' => '',
    ];

}