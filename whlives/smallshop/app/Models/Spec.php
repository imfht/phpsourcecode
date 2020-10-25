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
 * 商品规格
 * Class Article
 * @package App\Models
 */
class Spec extends BaseModels
{
    use SoftDeletes;

    const TYPE_IMAGE_ON = 1;
    const TYPE_IMAGE_OFF = 0;

    const TYPE_IMAGE_DESC = [
        self::TYPE_IMAGE_OFF => '否',
        self::TYPE_IMAGE_ON => '是'
    ];

    protected $table = 'spec';
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];

    /**
     * 获取规格值
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function specValue() {
        return $this->hasMany('App\Models\SpecValue');
    }
}
