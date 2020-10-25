<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-07-06 11:10
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;

/**
 * Class UserCart.
 */
class UserCart extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'count',
        'price',
        'product_id',
        'store_id',
        'user_id',
    ];

    /**
     * @var array
     */
    protected $setters = [
        'count' => 'null|0',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_user_carts';
}
