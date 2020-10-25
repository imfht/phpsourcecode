<?php

/**
 * 验证码管理
 */
namespace app\member\model;

use app\system\model\SystemModel;

class MemberVerifyModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'verify_id',
        'validate' => [
            'receive' => [
                'required' => ['', '接收方不能为空', 'must', 'add'],
            ],
            'code' => [
                'required' => ['', '验证码不能为空', 'must', 'add'],
            ],
        ],
        'format' => [
            'time' => [
                'function' => ['time', 'add'],
            ]
        ],
        'into' => '',
        'out' => '',
    ];

}