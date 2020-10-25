<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 14:56
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;

/**
 * Class ProductType.
 */
class ProductType extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'identification',
        'name',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_product_types';
}
