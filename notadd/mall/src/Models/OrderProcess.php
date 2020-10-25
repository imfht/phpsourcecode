<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 15:44
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;
use Notadd\Foundation\Member\Member;

/**
 * Class OrderProcess.
 */
class OrderProcess extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'order_id',
        'status',
        'user_id',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_order_processes';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Member::class, 'user_id');
    }
}
