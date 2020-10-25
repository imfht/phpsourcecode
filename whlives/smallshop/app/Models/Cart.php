<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/11
 * Time: 下午4:46
 */

namespace App\Models;

/**
 * 购物车
 * Class Adv
 * @package App\Models
 */
class Cart extends BaseModels
{
    //购买类型
    const TYPE_CART = 1;
    const TYPE_NOW = 2;
    const TYPE_SECKILL = 3;

    const TYPE_DESC = [
        self::TYPE_CART => '购物车',
        self::TYPE_NOW => '立即购买',
        self::TYPE_SECKILL => '秒杀'
    ];
    protected $table = 'cart';
    protected $guarded = ['id'];

}
