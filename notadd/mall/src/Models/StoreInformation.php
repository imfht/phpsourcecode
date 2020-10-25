<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-28 12:09
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;

/**
 * Class StoreInformation.
 */
class StoreInformation extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'address',
        'capital',
        'company',
        'contacts',
        'email',
        'employees',
        'licence_image',
        'licence_location',
        'licence_number',
        'licence_validity',
        'licence_sphere',
        'location',
        'store_id',
        'telephone',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_store_informations';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
