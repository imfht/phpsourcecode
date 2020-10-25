<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;

class SysAdmins extends Authenticatable
{
    protected $table = 'sys_admins';

    protected $fillable = ['email', 'nickname', 'active', 'password'];

    protected $hidden = ['password', 'remember_token'];

    public function roles()
    {
        return $this->belongsToMany('App\Models\SysRoles', 'sys_roles_admins', 'admins_id', 'roles_id');
    }

    /**
     * 返回当前用户所在的组
     * @return mixed
     */
    public function cachedRoles()
    {
        $userPrimaryKey = $this->primaryKey;
        $cacheKey = 'roles_for_user_'.$this->$userPrimaryKey;
        return Cache::tags('sys_roles')->remember($cacheKey, 86400, function () {
            $data =  $this->roles()->get();
            return $data;
        });
    }

    public function hasRole($name, $requireAll = false)
    {
        if (is_array($name)) {
            foreach ($name as $roleName) {
                $hasRole = $this->hasRole($roleName);

                if ($hasRole && !$requireAll) {
                    return true;
                } elseif (!$hasRole && $requireAll) {
                    return false;
                }
            }
            return $requireAll;
        } else {
            foreach ($this->cachedRoles() as $role) {
                if ($role->name == $name) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if user has a permission by its name.
     *
     * @param string|array $permission Permission string or array of permissions.
     * @param bool         $requireAll All permissions in the array are required.
     *
     * @return bool
     */
    public function canPermission($permission, $requireAll = false)
    {
        if (is_array($permission)) {
            foreach ($permission as $permName) {
                $hasPerm = $this->can($permName);

                if ($hasPerm && !$requireAll) {
                    return true;
                } elseif (!$hasPerm && $requireAll) {
                    return false;
                }
            }
            return $requireAll;
        } else {
            foreach ($this->cachedRoles() as $role) {
                // Validate against the Permission table
                foreach ($role->cachedPermissions() as $perm) {
                    if (str_is( $permission, $perm->name) ) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function canMenus($menu)
    {
        foreach ($this->cachedRoles() as $role) {
            foreach ($role->cachedMenus() as $me) {
                if (str_is($menu, $me->name)) {
                    return true;
                }
            }
        }
        return false;
    }

}
