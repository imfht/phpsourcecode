<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

/**
 * 售后图片
 * Class Goods
 * @package App\Models
 */
class RefundImage extends BaseModels
{

    protected $table = 'refund_image';
    protected $guarded = ['id'];

    public $timestamps = false;

}
