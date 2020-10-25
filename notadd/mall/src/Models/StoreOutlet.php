<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-28 14:10
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;

/**
 * Class StoreOutlet.
 */
class StoreOutlet extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'address',
        'bus_information',
        'name',
        'store_id',
        'telephone',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_store_outlets';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
