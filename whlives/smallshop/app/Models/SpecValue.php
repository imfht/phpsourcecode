<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 商品规格值
 * Class Article
 * @package App\Models
 */
class SpecValue extends BaseModels
{
    use SoftDeletes;

    protected $table = 'spec_value';
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];

}
