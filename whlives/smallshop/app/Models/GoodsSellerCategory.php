<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

/**
 * 商家商品分类
 * Class Goods
 * @package App\Models
 */
class GoodsSellerCategory extends BaseModels
{

    protected $table = 'goods_seller_category';
    protected $guarded = ['id'];

    public $timestamps = false;

}
