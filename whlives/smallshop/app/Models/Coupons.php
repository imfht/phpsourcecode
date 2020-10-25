<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 优惠券活动
 * Class Payment
 * @package App\Models
 */
class Coupons extends BaseModels
{
    use SoftDeletes;
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

    const TYPE_DESC = [
        self::TYPE_REDUCTION => '满减',
        self::TYPE_DISCOUNT => '折扣优惠',
    ];

    protected $table = 'coupons';
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];

}
