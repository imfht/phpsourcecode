<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;
use Illuminate\Support\Facades\DB;

/**
 * 交易单
 * Class Shop
 * @package App\Models
 */
class Trade extends BaseModels
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;
    const STATUS_DESC = [
        self::STATUS_ON => '已支付',
        self::STATUS_OFF => '待支付'
    ];

    //类型
    const TYPE_ORDER = 1;
    const TYPE_RECHARGE = 2;
    const TYPE_DESC = [
        self::TYPE_ORDER => '订单',
        self::TYPE_RECHARGE => '充值'
    ];

    //风险订单提示
    const FLAG_NO = 0;
    const FLAG_YES = 1;
    const FLAG_DESC = [
        self::FLAG_NO => '正常',
        self::FLAG_YES => '风险'
    ];

    protected $table = 'trade';
    protected $guarded = ['id'];

}
