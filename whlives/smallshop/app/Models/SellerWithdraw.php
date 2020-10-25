<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;

/**
 * 商家提现
 * Class Payment
 * @package App\Models
 */
class SellerWithdraw extends BaseModels
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;
    const STATUS_REFUND = 2;
    const STATUS_DEDUCT = 3;

    const STATUS_DESC = [
        self::STATUS_OFF => '待审核',
        self::STATUS_ON => '已经审核',
        self::STATUS_REFUND => '拒绝并退还资金',
        self::STATUS_DEDUCT => '拒绝不退还资金'
    ];

    protected $table = 'seller_withdraw';
    protected $guarded = ['id'];

}
