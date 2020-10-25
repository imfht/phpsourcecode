<?php
namespace app\common\dao;


use app\common\base\BaseDao;

interface RoleAbilitiesInterface extends BaseDao
{
    public function deleteDateByAbilitiesId($user_id); //根据用户ID(user_id)删除
    public function deleteDateByRoleId($role_id); //根据角色ID(role_id)删除
}