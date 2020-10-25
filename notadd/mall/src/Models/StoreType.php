<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-07-27 18:07
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;

/**
 * Class StoreType.
 */
class StoreType extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'amount_of_deposit',
        'name',
        'order',
    ];

    /**
     * @var array
     */
    protected $setters = [
        'amount_of_deposit' => 'null|0.00',
        'order'             => 'null|0',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_store_types';
}
