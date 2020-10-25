<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 品牌
 * Class Payment
 * @package App\Models
 */
class Brand extends BaseModels
{
    use SoftDeletes;
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '锁定'
    ];

    protected $table = 'brand';
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];

}
