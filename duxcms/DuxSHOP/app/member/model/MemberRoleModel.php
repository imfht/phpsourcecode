<?php

/**
 * 用户组管理
 */
namespace app\member\model;

use app\system\model\SystemModel;

class MemberRoleModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'role_id',
        'validate' => [
            'name' => [
                'len' => ['1, 20', '角色名称只能为英文数字或下划线', 'must', 'all'],
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