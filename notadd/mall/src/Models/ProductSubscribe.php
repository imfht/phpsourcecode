<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-23 15:29
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;

/**
 * Class ProductSubscribe.
 */
class ProductSubscribe extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'product_id',
        'store_id',
        'status',
        'user_id',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_product_subscribes';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
