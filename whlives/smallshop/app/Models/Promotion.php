<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;

/**
 * 促销活动
 * Class Payment
 * @package App\Models
 */
class Promotion extends BaseModels
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_OFF => '锁定',
        self::STATUS_ON => '审核'
    ];

    //活动类型
    const TYPE_REDUCTION = 1;//满减
    const TYPE_DISCOUNT = 2;//折扣优惠
    const TYPE_POINT = 3;//赠送积分
    const TYPE_COUPONS = 4;//送优惠券

    const TYPE_DESC = [
        self::TYPE_REDUCTION => '满减',
        self::TYPE_DISCOUNT => '折扣优惠',
        self::TYPE_POINT => '赠送积分',
        self::TYPE_COUPONS => '送优惠券',
    ];

    protected $table = 'promotion';
    protected $guarded = ['id'];

}
