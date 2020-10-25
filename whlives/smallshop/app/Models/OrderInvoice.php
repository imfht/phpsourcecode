<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;

/**
 * 订单发票
 * Class Payment
 * @package App\Models
 */
class OrderInvoice extends BaseModels
{
    //配送方式
    const TYPE_PERSONAL = 1;
    const TYPE_ENTERPRISE = 2;
    const TYPE_DESC = [
        self::TYPE_PERSONAL => '个人',
        self::TYPE_ENTERPRISE => '企业'
    ];

    protected $table = 'order_invoice';
    protected $guarded = ['id'];

    public $timestamps = false;
}
