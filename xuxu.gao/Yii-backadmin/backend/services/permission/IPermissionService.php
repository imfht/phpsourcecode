<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/26
 * Time: 11:06
 */

namespace backend\services\permission;


interface IPermissionService {

    /**
     * 添加权限
     * @param array $params
     * @return mixed
     */
    public function addPermission($params = []);

    /**
     * 权限列表
     * @param array $params
     * @return mixed
     */
    public function permissionList($params = []);

    /**
     * 记录数量
     * @param array $params
     * @return mixed
     */
    public function permissionCount($params = []);

    /**
     * 查询分组后的权限列表
     * @return mixed
     */
    public function permissionGroupByTypeName();

    /**
     * 根据条件查询数据
     * @param array $params
     * @return mixed
     */
    public function queryPermission($params = []);

    /**
     * 更新权限
     * @param array $params
     * @return mixed
     */
    public function updatePermission($params = []);

    /**
     * 删除权限
     * @param array $params
     * @return mixed
     */
    public function deletePermission($params = []);

    /**
     * 返回多个数据
     * @param array $params
     * @return mixed
     */
    public function queryAllPermission($params = []);
}