<?php
/**
 * Created by PhpStorm.
 * User: carl
 * Date: 2017/3/27
 * Time: 下午4:10
 */

namespace Services;


class PermissionsService extends ServiceAbstract
{
    public function model()
    {
        return \App\Models\SysPermissions::class;
    }

    public function all()
    {
        $list = $this->model->get();
        $data = getSubTree($list);
        return $data;
    }

    public function tree()
    {
        $list = $this->model->get();
        $data = getTree($list);
        return $data;
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function store(array $attributes)
    {
        $data['name']           = str_is($attributes['name'], '#') ? '#-'.time() : $attributes['name'];
        $data['display_name']   = $attributes['display_name'];
        $data['pid']            = $attributes['pid'];
        $data['sort']           = $attributes['sort'];
        $this->model->create($data);
        return true;
    }

    public function update(array $attributes, $id)
    {
        $permission = $this->findById($id);
        $permission->name = $attributes['name'];
        $permission->display_name = $attributes['display_name'];
        $permission->pid = $attributes['pid'];
        $permission->sort = $attributes['sort'];
        $result = $permission->save();
        return $result ? true : false;
    }

    public function destroy($id)
    {
        $permission = $this->model->findOrFail($id);
        //移除权限的关联关系
        $permission->roles()->detach();
        $permission->delete();
        return true;
    }


}