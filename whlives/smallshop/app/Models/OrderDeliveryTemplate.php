<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/22
 * Time: 3:33 PM
 */

namespace App\Models;

/**
 * 快递单打印模板
 * Class AdminUser
 * @package App\Models
 */
class OrderDeliveryTemplate extends BaseModels
{

    protected $table = 'order_delivery_template';
    protected $guarded = ['id'];

}
