<?php

/**
 * 通知管理
 */
namespace app\system\model;

use app\system\model\SystemModel;

class SystemNoticeModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'notice_id',
        'validate' => [
            'content' => [
                'len' => ['5', '通知内容至少5个字符', 'must', 'all'],
            ],
        ],
        'format' => [
            'time' => [
                'function' => ['time', 'add'],
            ],
        ],
        'into' => '',
        'out' => '',
    ];

}