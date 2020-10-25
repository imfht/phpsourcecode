<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ActionLog
 *
 * @property-read \App\Models\Admin $admin
 * @property-read mixed $data
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActionLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActionLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ActionLog query()
 * @mixin \Eloquent
 */
class ActionLog extends Model
{
    protected $fillable = ['admin_id','data'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * data数据修饰器
     * @param $value
     * @return mixed
     */
    public function getDataAttribute($value)
    {
        return json_decode($value,true);
    }
}
