<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/25
 * Time: 15:02
 */

namespace backend\services\role;


interface IRoleService {

    /**
     * 添加角色
     * @param array $params
     * @return mixed
     */
    public function addRole($params = []);

    /**
     * 角色列表
     * @param array $params
     * @return mixed
     */
    public function roleList($params = []);

    /**
     * @param array $params
     * @return mixed
     */
    public function roleCount($params = []);

    /**
     * 权限分配
     * @param array $params
     * @return mixed
     */
    public function assignRole($params = []);

    /**
     * 更新角色数据
     * @param array $params
     * @return mixed
     */
    public function roleUpdate($params = []);

    /**
     * 根据条件获取权限数据
     * @param array $params
     * @return mixed
     */
    public function queryRoleByWhere($params = []);

    /**
     * 删除角色
     * @param array $params
     * @return mixed
     */
    public function deleteRole($params = []);

    /**
     * 根据条件获取多条数据记录
     * @param array $params
     * @return mixed
     */
    public function queryAllRoleByWhere($params = []);

}