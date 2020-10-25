<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SysRoles extends Model
{
    protected $table = 'sys_roles';

    public function perm()
    {
        return $this->belongsToMany('App\Models\SysPermissions', 'sys_roles_permissions', 'roles_id', 'permissions_id');
    }

    public function menu()
    {
        return $this->belongsToMany('App\Models\SysMenus', 'sys_roles_menus', 'roles_id', 'menus_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\SysAdmins', 'sys_roles_admins', 'roles_id', 'admins_id');
    }

    public function cachedPermissions()
    {
        $rolePrimaryKey = $this->primaryKey;
        $cacheKey = 'permissions_for_role_'.$this->$rolePrimaryKey;
        return Cache::tags('sys_permissions')->remember($cacheKey, 86400, function () {
            return $this->perm()->get();
        });
    }

    public function cachedMenus()
    {
        $rolePrimaryKey = $this->primaryKey;
        $cacheKey = 'menus_for_role_'.$this->$rolePrimaryKey;
        return Cache::tags('sys_menus')->remember($cacheKey, 86400, function () {
            return $this->menu()->get();
        });
    }

    public function cleanCache()
    {
        Cache::tags('sys_permissions')->flush();
        Cache::tags('sys_menus')->flush();
    }


}
