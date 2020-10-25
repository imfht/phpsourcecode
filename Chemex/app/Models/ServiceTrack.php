<?php

namespace App\Models;

use Dcat\Admin\Admin;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(string $key, string $value)
 */
class ServiceTrack extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'service_tracks';

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

    public function service()
    {
        return $this->hasOne(ServiceRecord::class, 'id', 'service_id');
    }

    public function device()
    {
        return $this->hasOne(DeviceRecord::class, 'id', 'device_id');
    }
}
