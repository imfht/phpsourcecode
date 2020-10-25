<?php

namespace App\Models;

use Dcat\Admin\Admin;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(string $key, string $value)
 */
class CheckRecord extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'check_records';

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
     * 雇员
     * @return HasOne
     */
    public function user()
    {
        return $this->hasOne(AdminUser::class, 'id', 'user_id');
    }
}
