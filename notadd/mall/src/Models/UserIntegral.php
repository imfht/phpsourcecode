<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-29 15:16
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;

/**
 * Class UserIntegral.
 */
class UserIntegral extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'integral',
        'user_id',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_user_integrals';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function log()
    {
        return $this->hasMany(UserIntegralLog::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
