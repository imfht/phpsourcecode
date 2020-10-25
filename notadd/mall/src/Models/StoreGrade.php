<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-07-15 18:55
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;

/**
 * Class StoreGrade.
 */
class StoreGrade extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'application_instruction',
        'can_claim',
        'can_upload',
        'level',
        'name',
        'price',
        'publish_limit',
        'upload_limit',
    ];

    /**
     * @var array
     */
    protected $setters = [
        'can_claim'     => 'null|0',
        'can_upload'    => 'null|0',
        'level'         => 'null|0',
        'publish_limit' => 'null|0',
        'upload_limit'  => 'null|0',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_store_grades';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stores()
    {
        return $this->hasMany(Store::class);
    }
}
