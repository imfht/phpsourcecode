<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;

/**
 * 提现
 * Class Payment
 * @package App\Models
 */
class Withdraw extends BaseModels
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

    //提现方式
    const TYPE_BANK = 1;
    const TYPE_ALIPAY = 2;
    const TYPE_WECHAR = 3;

    const TYPE_DESC = [
        self::TYPE_BANK => '银行',
        self::TYPE_ALIPAY => '支付宝',
        self::TYPE_WECHAR => '微信',
    ];

    protected $table = 'withdraw';
    protected $guarded = ['id'];

}
