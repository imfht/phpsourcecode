<?php
/**
 * Created by PhpStorm.
 * User: carl
 * Date: 2017/3/27
 * Time: 上午9:04
 */

namespace Services;


class RolesService extends ServiceAbstract
{
    public $role;

    public function model()
    {
        return \App\Models\SysRoles::class;
    }

    public function getUsersForRoles($rolesId)
    {
        $role = $this->findById($rolesId);
        return $role->users()->orderBy('id', 'desc')->paginate();
    }

    /**
     * 获取所有角色
     * @param array $column
     * @return mixed
     */
    public function all($column = ['*'])
    {
        return $this->model->get($column);
    }

    /**
     * 获取单条数据, 同一个实例的同一个数据对象只查找一次数据库
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        if (empty($this->role[$id])) {
            $this->role[$id] = $this->model->findOrFail($id);
        }
        return $this->role[$id];
    }

    /**
     * 添加角色
     * @param array $attributes
     * @return mixed
     */
    public function store(array $attributes)
    {
        $this->model->name = $attributes['name'];
        $this->model->display_name = $attributes['display_name'];
        $result = $this->model->save();
        return $result;
    }

    /**
     * 删除角色
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $role = $this->findById($id);
        //首先移除所有的关系
        $this->saveMenu($id, '');
        $this->savePermission($id, '');
        return $role->delete();
    }

    /**
     * 获取权限集合
     * @param $roleId
     */
    public function perm($roleId)
    {
        $role = $this->findById($roleId);
        return $role->perm;
    }

    /**
     * 获取菜单集合
     * @param $roleId
     */
    public function menu($roleId)
    {
        $role = $this->findById($roleId);
        return $role->menu;
    }

    /**
     * 批量更新权限
     * @param $roleId
     * @param $permission
     */
    public function savePermission($roleId, $permission)
    {
        $role = $this->findById($roleId);
        if (!empty($permission)) {
            $role->perm()->sync($permission);
        } else {
            $role->perm()->detach();
        }
        $role->cleanCache();
    }

    /**
     * 插入权限
     * @param $roleId
     * @param $permission
     */
    public function attachPermission($roleId, $permission)
    {
        $role = $this->findById($roleId);
        if (is_object($permission)) {
            $permission = $permission->getKey();
        }
        $role->perm()->attach($permission);
        $role->cleanCache();
    }

    /**
     * 移除权限
     * @param $roleId
     * @param $permission
     */
    public function detachPermission($roleId, $permission)
    {
        $role = $this->findById($roleId);
        if (is_object($permission)) {
            $permission = $permission->getKey();
        }
        $role->perm()->detach($permission);
        $role->cleanCache();
    }

    /**
     * 批量同步菜单权限
     * @param $roleId
     * @param $menu
     * @param bool $sync
     */
    public function saveMenu($roleId, $menu, $sync=true)
    {
        $role = $this->findById($roleId);
        if (!empty($menu)) {
            if ($sync) {
                $role->menu()->sync($menu);
            } else {
                $role->menu()->syncWithoutDetaching($menu);
            }
        } else {
            $role->menu()->detach();
        }
        $role->cleanCache();
    }

    /**
     * 插入菜单权限
     * @param $roleId
     * @param $menu
     */
    public function attachMenu($roleId, $menu)
    {
        $role = $this->findById($roleId);
        if (is_object($menu)) {
            $menu = $menu->getKey();
        }
        $role->menu()->attche($menu);
        $role->cleanCache();
    }

    /**
     * 移除菜单权限
     * @param $roleId
     * @param $menu
     */
    public function detachMenu($roleId, $menu)
    {
        $role = $this->findById($roleId);
        if (is_object($menu)) {
            $menu = $menu->getKey();
        }
        $role->menu()->detach($menu);
        $role->cleanCache();
    }

}