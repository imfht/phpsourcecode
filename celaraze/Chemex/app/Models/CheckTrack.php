<?php

namespace App\Models;

use Dcat\Admin\Admin;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(string $key, string $value)
 */
class CheckTrack extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'check_tracks';

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

    public function user()
    {
        return $this->hasOne(AdminUser::class, 'id', 'checker');
    }

    public function check()
    {
        return $this->hasOne(CheckRecord::class, 'id', 'check_id');
    }
}
