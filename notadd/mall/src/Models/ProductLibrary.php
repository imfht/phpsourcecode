<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-07-12 11:13
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;

/**
 * Class ProductLibrary.
 */
class ProductLibrary extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'barcode',
        'brand_id',
        'category_id',
        'delivery_area',
        'description',
        'description_mobile',
        'flow_marketing',
        'image',
        'inventory',
        'inventory_warning',
        'name',
        'price_range',
        'production_place',
        'public_praise',
        'selling_point',
        'size',
        'weight',
    ];

    /**
     * @var array
     */
    protected $setters = [
        'brand_id'    => 'null|0',
        'category_id' => 'null|0',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_product_libraries';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo(ProductBrand::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
