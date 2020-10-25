<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/18
 * Time: 15:26
 */

namespace backend\services\user;


interface IUserService {

    /**
     * 添加用户
     * @param null $params
     * @return mixed
     */
    public function addUser($params  = null);

    /**
     * 用户列表
     * @param null $params
     * @return mixed
     */
    public function userList($params);

    /**
     * 用户数量
     * @param $params
     * @return mixed
     */
    public function userCount($params);

    /**
     * 根据用户id查询用户
     * @param $uid
     * @return mixed
     */
    public function getUserById($uid);

    /**
     * 更新用户
     * @param $params
     * @return mixed
     */
    public function updateUser($params);

    /**
     * 根据id删除用户
     * @param $uid
     * @return mixed
     */
    public function deleteUserById($uid);

}