<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Role
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Admin[] $admins
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Rule[] $rules
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role query()
 * @mixin \Eloquent
 */
class Role extends Model
{
    protected $fillable = ['name', 'remark', 'order', 'status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function admins()
    {
        return $this->belongsToMany(Admin::class)->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rules()
    {
        return $this->belongsToMany(Rule::class,'role_auth')->withTimestamps();
    }

    /**
     * 获取显示的权限
     * @return mixed
     */
    public function rulesPublic()
    {
        return $this->rules()->public()->orderBy('sort','asc')->get();
    }

}
