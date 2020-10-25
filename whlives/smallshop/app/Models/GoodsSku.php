<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

/**
 * 商品sku
 * Class Goods
 * @package App\Models
 */
class GoodsSku extends BaseModels
{

    //状态
    const STATUS_DEL = 99;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_DEL => '删除'
    ];

    protected $table = 'goods_sku';
    protected $guarded = ['id'];

    public $timestamps = false;

}
