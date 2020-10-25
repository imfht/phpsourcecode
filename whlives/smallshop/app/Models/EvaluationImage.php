<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

/**
 * 商品评价图片
 * Class EvaluationImage
 * @package App\Models
 */
class EvaluationImage extends BaseModels
{

    protected $table = 'evaluation_image';
    protected $guarded = ['id'];

    public $timestamps = false;

}
