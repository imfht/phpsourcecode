<?php

namespace App\Models;

use Dcat\Admin\Admin;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(string $key, string $value)
 */
class DeviceRecord extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'device_records';

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

    /**
     * 设备分类
     * @return HasOne
     */
    public function category()
    {
        return $this->hasOne(DeviceCategory::class, 'id', 'category_id');
    }

    /**
     * 制造商
     * @return HasOne
     */
    public function vendor()
    {
        return $this->hasOne(VendorRecord::class, 'id', 'vendor_id');
    }

    /**
     * 设备下所有硬件
     * @return HasManyThrough
     */
    public function hardware()
    {
        return $this->hasManyThrough(
            HardwareRecord::class,  // 远程表
            HardwareTrack::class,   // 中间表
            'device_id',    // 中间表对主表的关联字段
            'id',   // 远程表对中间表的关联字段
            'id',   // 主表对中间表的关联字段
            'hardware_id'); // 中间表对远程表的关联字段
    }

    /**
     * 设备下所有软件
     * @return HasManyThrough
     */
    public function software()
    {
        return $this->hasManyThrough(
            SoftwareRecord::class,  // 远程表
            SoftwareTrack::class,   // 中间表
            'device_id',    // 中间表对主表的关联字段
            'id',   // 远程表对中间表的关联字段
            'id',   // 主表对中间表的关联字段
            'software_id'); // 中间表对远程表的关联字段
    }
}
