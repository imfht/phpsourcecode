<?php

namespace App\Models;

use Dcat\Admin\Admin;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(string $key, string $value)
 */
class HardwareRecord extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'hardware_records';

    /**
     * 模型的 "booted" 方法
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function ($model) {
            $model->creator = Admin::user()->name;
        });
    }

    public function category()
    {
        return $this->hasOne(HardwareCategory::class, 'id', 'category_id');
    }

    public function vendor()
    {
        return $this->hasOne(VendorRecord::class, 'id', 'vendor_id');
    }
}
