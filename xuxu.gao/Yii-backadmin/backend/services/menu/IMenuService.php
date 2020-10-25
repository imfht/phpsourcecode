<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/20
 * Time: 9:32
 */

namespace backend\services\menu;


interface IMenuService {

    /**
     * 查询菜单
     * $where 条件数组
     * @return mixed
     */
    public function queryMenus($where = []);

    /**
     * 添加菜单
     * @param array $params
     * @return mixed
     */
    public function menuAdd($params = []);

    /**
     * 菜单列表
     * @param array $params
     * @return mixed
     */
    public function menuList($params = []);

    /**
     * 数量查询
     * @param array $params
     * @return mixed
     */
    public function menuCount($params = []);

    /**
     * 根据id查询数据
     * @param $id
     * @return mixed
     */
    public function menuById($id);

    /**
     * 菜单更新
     * @param array $params
     * @return mixed
     */
    public function menuUpdate($params = []);

    /**
     * 菜单列表
     * @param $list
     * @param int $pid
     * @return mixed
     */
    public function menuGroup($list);

    /**
     * 根据id删除菜单
     * @param $id
     * @return mixed
     */
    public function menuDelete($id);
}