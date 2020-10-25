<?php

namespace App\Models;

use Dcat\Admin\Admin;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $key, string $value)
 */
class AdminUser extends Model
{
    use HasFactory;
    use HasDateTimeFormatter;

    protected $table = 'admin_users';

    /**
     * 模型的 "booted" 方法
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function () {
            if (config('admin.demo')) {
                abort(401, '演示模式下不允许修改');
            }
        });
    }
}
