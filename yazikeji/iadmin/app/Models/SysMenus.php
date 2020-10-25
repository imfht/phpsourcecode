<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SysMenus extends Model
{
    protected $table = 'sys_menus';

    protected $fillable = ['name', 'display_name', 'uri', 'sort', 'pid'];

    public function roles()
    {
        return $this->belongsToMany('App\Models\SysRoles', 'sys_roles_menus', 'menus_id', 'roles_id');
    }
}
