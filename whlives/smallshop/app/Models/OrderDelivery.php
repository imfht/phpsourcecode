<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;

/**
 * 订单物流
 * Class Payment
 * @package App\Models
 */
class OrderDelivery extends BaseModels
{
    protected $table = 'order_delivery';
    protected $guarded = ['id'];
}
