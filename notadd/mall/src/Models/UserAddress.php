<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-29 17:30
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;

/**
 * Class UserAddress.
 */
class UserAddress extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'address',
        'is_default',
        'location',
        'name',
        'phone',
        'user_id',
    ];

    /**
     * @var array
     */
    protected $setters = [
        'is_default' => 'null|0',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_user_addresses';
}
