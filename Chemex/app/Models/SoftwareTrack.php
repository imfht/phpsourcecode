<?php

namespace App\Models;

use Dcat\Admin\Admin;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(string $key, string $value)
 */
class SoftwareTrack extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'software_tracks';

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

    public function software()
    {
        return $this->hasOne(SoftwareRecord::class, 'id', 'software_id');
    }

    public function device()
    {
        return $this->hasOne(DeviceRecord::class, 'id', 'device_id');
    }
}
