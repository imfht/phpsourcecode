<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-29 15:17
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;

/**
 * Class UserIntegralLog.
 */
class UserIntegralLog extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'comment',
        'integral',
        'user_id',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_user_integral_logs';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
