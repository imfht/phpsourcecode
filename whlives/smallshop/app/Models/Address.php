<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/11
 * Time: 下午4:46
 */

namespace App\Models;

/**
 * 收货地址
 * Class Adv
 * @package App\Models
 */
class Address extends BaseModels
{

    //是否默认
    const DEFAULT_OFF = 0;
    const DEFAULT_ON = 1;

    const DEFAULT_DESC = [
        self::DEFAULT_OFF => '否',
        self::DEFAULT_ON => '是'
    ];
    protected $table = 'address';
    protected $guarded = ['id'];

}
