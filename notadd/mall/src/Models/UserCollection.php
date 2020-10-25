<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-07-05 14:15
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;

/**
 * Class UserFollow.
 */
class UserCollection extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'product_id',
        'store_id',
        'user_id',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_user_collections';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
