<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/11
 * Time: 下午4:46
 */

namespace App\Models;

/**
 * 广告
 * Class Adv
 * @package App\Models
 */
class Adv extends BaseModels
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '锁定'
    ];

    //跳转连接类型
    const TARGET_TYPE_URL = 1;
    const TARGET_TYPE_ARTICLE = 2;
    const TARGET_TYPE_THEME = 3;
    const TARGET_TYPE_GOODS = 4;
    const TARGET_TYPE_DESC = [
        self::TARGET_TYPE_URL => '链接',
        self::TARGET_TYPE_ARTICLE => '文章',
        self::TARGET_TYPE_THEME => '专题',
        self::TARGET_TYPE_GOODS => '商品'
    ];

    protected $table = 'adv';
    protected $guarded = ['id'];

}
