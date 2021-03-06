<?php

namespace App\Models;

use Dcat\Admin\Admin;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(string $key, string $value)
 */
class StaffDepartment extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'staff_departments';

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

    public function parent()
    {
        return $this->hasOne(StaffDepartment::class, 'id', 'parent_id');
    }
}
