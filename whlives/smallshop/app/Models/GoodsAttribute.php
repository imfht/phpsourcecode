<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

/**
 * 商品属性
 * Class Goods
 * @package App\Models
 */
class GoodsAttribute extends BaseModels
{

    protected $table = 'goods_attribute';
    protected $guarded = ['id'];

    public $timestamps = false;

}
