<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-28 17:21
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;

/**
 * Class ProductRatePicture.
 */
class ProductRatePicture extends Model
{
    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @var string
     */
    protected $table = 'mall_product_rate_pictures';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rate()
    {
        return $this->belongsTo(ProductRate::class);
    }
}
