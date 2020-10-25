<?php

/**
 * 物流配置
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderConfigExpressModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'express_id',
    ];

}