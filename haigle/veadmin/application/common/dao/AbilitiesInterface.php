<?php
namespace app\common\dao;


use app\common\base\BaseDao;

interface AbilitiesInterface extends BaseDao
{
    public function getRoleAbilities($role_id); //获取所有权限

    public function trueAbilities($user_id); //所有的权限
}