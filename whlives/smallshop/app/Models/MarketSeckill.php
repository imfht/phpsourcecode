<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/11
 * Time: 下午4:46
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 秒杀活动
 * Class Adv
 * @package App\Models
 */
class MarketSeckill extends BaseModels
{
    use SoftDeletes;
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '锁定'
    ];

    const GOODS_REDIS_KEY = 'seckill_goods:';//秒杀商品rediskey前缀

    protected $table = 'market_seckill';
    protected $guarded = ['id'];
}
