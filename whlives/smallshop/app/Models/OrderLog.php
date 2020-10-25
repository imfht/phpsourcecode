<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;

/**
 * 订单日志
 * Class Payment
 * @package App\Models
 */
class OrderLog extends BaseModels
{

    //用户类型
    const USER_TYPE_MEMBER = 0;
    const USER_TYPE_SYSTEM = 1;
    const USER_TYPE_ADMIN = 2;
    const USER_TYPE_SELLER = 3;

    const USER_TYPE_DESC = [
        self::USER_TYPE_MEMBER => '用户',
        self::USER_TYPE_SYSTEM => '系统',
        self::USER_TYPE_ADMIN => '管理员',
        self::USER_TYPE_SELLER => '商家',
    ];
    protected $table = 'order_log';
    protected $guarded = ['id'];
}
