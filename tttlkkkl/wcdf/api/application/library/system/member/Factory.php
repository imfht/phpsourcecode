<?php

/**
 * 模块接口列表
 * Date: 16-10-24
 * Time: 上午4:16
 * author :李华 yehong0000@163.com
 */

namespace system\member;

use system\member\logic\DepartmentLogic;
use system\member\logic\OrganizationLogic;

class Factory
{
    public static function __callStatic($name, $arguments)
    {
        throw new \Exception('Bad Request',400);
    }

    /**
     * 获取部门信息
     *
     * @param $id
     *
     * @return mixed
     * @throws \Exception
     */
    static public function getDepartment($data)
    {
        return DepartmentLogic::getInstance()->get(isset($data['id']) ? $data['id'] : 0);
    }

    /**
     * 添加部门
     *
     * @param $data
     */
    static public function postDepartment($data)
    {
        return DepartmentLogic::getInstance()->post($data);
    }

    /**
     * 更新部门
     *
     * @param $data
     *
     * @return bool
     * @throws \Exception
     */
    public static function putDepartment($data)
    {
        return DepartmentLogic::getInstance()->put($data);
    }

    /**
     * 删除部门
     *
     * @param $id
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteDepartment($data)
    {
        return DepartmentLogic::getInstance()->delete($data['id']);
    }

    /**
     * 更新组织架构
     *
     * @param $data
     *
     * @return bool|string
     */
    public function putOrganization($data)
    {
        return OrganizationLogic::getInstance()->put();
    }
}