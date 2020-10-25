<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

/**
 * 商家地址
 * Class Member
 * @package App\Models
 */
class SellerAddress extends BaseModels
{
    //是否默认
    const DEFAULT_OFF = 0;
    const DEFAULT_ON = 1;

    const DEFAULT_DESC = [
        self::DEFAULT_OFF => '否',
        self::DEFAULT_ON => '是'
    ];
    protected $table = 'seller_address';
    protected $guarded = ['id'];
}
