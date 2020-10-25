<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;

/**
 * 快递公司
 * Class ExpressCompany
 * @package App\Models
 */
class ExpressCompany extends BaseModels
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '锁定'
    ];

    //支付方式
    const PAY_TYPE_NOW = 1;
    const PAY_TYPE_TO_PAY = 2;
    const PAY_TYPE_MONTH = 3;
    const PAY_TYPE_THIRD = 4;

    const PAY_TYPE_DESC = [
        self::PAY_TYPE_NOW => '现付',
        self::PAY_TYPE_TO_PAY => '到付',
        self::PAY_TYPE_MONTH => '月结',
        self::PAY_TYPE_THIRD => '第三方支付(仅SF支持)'
    ];

    protected $table = 'express_company';
    protected $guarded = ['id'];

}
