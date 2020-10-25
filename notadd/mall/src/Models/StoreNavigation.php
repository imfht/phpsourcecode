<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-29 11:41
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;

/**
 * Class StoreNavigation.
 */
class StoreNavigation extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'is_show',
        'name',
        'order',
        'parent_target',
        'url',
    ];

    /**
     * @var array
     */
    protected $setters = [
        'is_show'       => 'null|0',
        'order'         => 'null|0',
        'parent_target' => 'null|0',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_store_navigations';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
