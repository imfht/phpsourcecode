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
 * 商品属性
 * Class Article
 * @package App\Models
 */
class Attribute extends BaseModels
{
    use SoftDeletes;

    const INPUT_TYPE_SELECT = 'select';
    const INPUT_TYPE_CHECKBOX = 'checkbox';
    const INPUT_TYPE_RADIO = 'radio';
    const INPUT_TYPE_TEXT = 'text';

    const INPUT_TYPE_DESC = [
        self::INPUT_TYPE_SELECT => '下拉框',
        self::INPUT_TYPE_CHECKBOX => '多选',
        self::INPUT_TYPE_RADIO => '单选',
        self::INPUT_TYPE_TEXT => '文本框'
    ];

    protected $table = 'attribute';
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];

    /**
     * 获取属性值
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function attrValue() {
        return $this->hasMany('App\Models\AttributeValue');
    }
}
