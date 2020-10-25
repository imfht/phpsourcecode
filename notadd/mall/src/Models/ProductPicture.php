<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-29 14:45
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;

/**
 * Class ProductPicture.
 */
class ProductPicture extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'product_id',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_product_pictures';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
