<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;

/**
 * 收藏
 * Class Payment
 * @package App\Models
 */
class Favorite extends BaseModels
{
    //状态
    const TYPE_GOODS = 1;
    const TYPE_SELLER = 2;
    const TYPE_ARTICLE = 3;

    const TYPE_DESC = [
        self::TYPE_GOODS => '商品',
        self::TYPE_SELLER => '商家',
        self::TYPE_ARTICLE => '文章'
    ];

    protected $table = 'favorite';
    protected $guarded = ['id'];

}
