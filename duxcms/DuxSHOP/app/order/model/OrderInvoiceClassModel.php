<?php

/**
 * 发票分类
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderInvoiceClassModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'class_id',
    ];

}