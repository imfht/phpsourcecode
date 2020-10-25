<?php

/**
 * 广告位管理
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteAdvPositionModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'pos_id',
        'validate' => [
            'name' => [
                'len' => ['1, 250', '广告位输入不正确!', 'must', 'all'],
            ],
            'label' => [
                'len' => ['1,50', '标签只能为1~50个字符', 'must', 'all'],
                'unique' => ['', '已存在相同广告位标签', 'must', 'all'],
            ],
        ],
        'format' => [
            'name' => [
                'function' => ['html_clear', 'all'],
            ],
        ],
        'into' => '',
        'out' => '',
    ];

    

}