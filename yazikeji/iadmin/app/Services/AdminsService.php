<?php
/**
 * Created by PhpStorm.
 * User: carl
 * Date: 2017/3/29
 * Time: 上午9:37
 */

namespace Services;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminsService extends ServiceAbstract
{
    public function model()
    {
        return \App\Models\SysAdmins::class;
    }

    /**
     * 分页展示数据
     * @param $pages
     * @return mixed
     */
    public function paginate($pages)
    {
        $pages = $pages ? $pages : 30;
        return $this->model->orderBy('id', 'desc')->paginate($pages);
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * 添加管理员
     * @param array $attributes
     * @return bool
     */
    public function store(array $attributes)
    {
        DB::beginTransaction();
        try {
            $password = bcrypt($attributes['password']);
            $admin = $this->model->create([
                'email'     => $attributes['email'],
                'nickname'  => $attributes['nickname'],
                'password'  => $password,
                'active'    => $attributes['active']
            ]);

            $admin->roles()->sync([$attributes['role']]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * 更新管理员信息
     * @param array $attributes
     * @param $id
     * @return bool
     */
    public function update(array $attributes, $id)
    {
        try {
            $admin = $this->findById($id);
            $up = false;
            foreach ($attributes as $column=>$attribute) {
                if ($admin->$column == $attribute) {
                    unset($attributes[$column]);
                    continue;
                }
                $up = true;
                $admin->$column = $attribute;
            }
            $admin->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updatePassword($attributes, $id)
    {
        try {
            //验证原密码是否正确
            $admin = $this->findById($id);

            if (!Hash::check($attributes['oldPassword'], $admin->password)) {
                throw new \Exception('旧密码输入错误');
            }

            if ($attributes['password'] !== $attributes['password_confirmation']) {
                throw new \Exception('重复密码输入不正确');
            }

            if (Hash::check($attributes['password'], $admin->password)) {
                throw new \Exception('数据错误, 密码没有修改');
            }
            $admin->password = Hash::make($attributes['password']);
            $admin->save();
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    /**
     * 删除管理员
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        //获取当前管理员对象
        $admin = $this->findById($id);
        //移除管理员与角色的关系
        $admin->roles()->detach();
        //删除管理员
        $admin->delete();
        return true;
    }

}