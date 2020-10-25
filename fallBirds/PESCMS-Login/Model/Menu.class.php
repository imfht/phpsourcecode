<?php

/**
 * PESCMS for PHP 5.4+
 *
 * Copyright (c) 2014 PESCMS (http://www.pescms.com)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace Model;

/**
 * 菜单模型
 */
class Menu extends \Core\Model\Model {

    /**
     * 生成后台菜单
     */
    public static function menu($groupId = '') {
        $condition = "";
        if (!empty($groupId) && $_SESSION['admin']['user_id'] > '1') {
            $group = \Model\Content::findContent('user_group', $groupId, 'user_group_id');
            $condition .= "m.menu_id in ({$group['user_group_menu']})";
        }


        $result = self::db('menu AS m')->field("m.*, IF(parent.top_id IS NULL, m.menu_id, parent.top_id) AS top_id, IF(parent.top_listsort IS NULL, '0', parent.top_listsort) AS top_listsort, IF(parent.top_name IS NULL, m.menu_name, top_name) AS top_name, menu_icon")->join("(SELECT `menu_id` AS top_id, `menu_name` AS top_name, `menu_pid` AS top_pid, `menu_listsort` AS top_listsort FROM `" . self::$modelPrefix . "menu` where menu_pid = 0) AS parent ON parent.top_id = m.menu_pid")->where($condition)->order('top_listsort desc, m.menu_listsort desc, m.menu_id asc')->select();

        foreach ($result as $key => $value) {
            if ($value['menu_pid'] == 0) {
                $menu[$value['top_name']]['menu_id'] = $value['top_id'];
                $menu[$value['top_name']]['menu_name'] = $value['top_name'];
                $menu[$value['top_name']]['menu_icon'] = $value['menu_icon'];
                $menu[$value['top_name']]['menu_listsort'] = $value['menu_listsort'];
            }
        }
        foreach ($result as $key => $value) {
            if (!empty($menu[$value['top_name']]) && $value['menu_pid'] != 0) {
                $menu[$value['top_name']]['menu_child'][] = $value;
            }
        }
        return $menu;
    }

    /**
     * 根据菜单获取标题
     */
    public static function getTitleWithMenu() {
        $result = self::db('menu')->field('menu_name')->where('menu_url = :menu_url')->find(array('menu_url' => 'Admin-' . MODULE . "-" . ACTION));
        return $result['menu_name'];
    }

    /**
     * 顶级菜单
     */
    public static function topMenu() {
        return self::db('menu')->where('menu_pid = 0')->order('menu_listsort desc, menu_id asc')->select();
    }

    /**
     * 查找菜单
     * @param type $menuId 菜单ID
     */
    public static function findMenu($menuId) {
        return self::db('menu')->where('menu_id = :menu_id')->find(array('menu_id' => $menuId));
    }

    /**
     * 添加菜单
     */
    public static function addMenu() {
        $data = self::baseForm();
        $addResult = self::db('menu')->insert($data);

        if ($addResult == false) {
            self::error('添加菜单失败');
        }
        return $addResult;
    }

    /**
     * 更新菜单
     */
    public static function updateMenu() {
        $data = self::baseForm();

        $updateResult = self::db('menu')->where('menu_id = :menu_id')->update($data);

        if ($updateResult == false) {
            self::error('更新菜单失败');
        }
        return true;
    }

    /**
     * 菜单基础表单
     */
    public static function baseForm() {

        if (self::p('method') == 'PUT') {
            $data['noset']['menu_id'] = self::isP('menu_id', '丢失菜单ID');
            if (!self::findMenu($data['noset']['menu_id'])) {
                self::error('不存在的菜单');
            }
        }

        if ($_POST['menu_pid'] < '0') {
            self::error('请选顶级菜单');
        } elseif ($_POST['menu_pid'] > '0') {

            if (!self::findMenu($_POST['menu_pid'])) {
                self::error('不存在的菜单');
            }
            $data['menu_url'] = self::isP('menu_url', '请填写菜单的地址');
        }
        $data['menu_pid'] = (int) $_POST['menu_pid'];
        $data['menu_name'] = self::isP('menu_name', '请填写菜单名称');

        $data['menu_icon'] = empty($_POST['menu_icon']) ? 'am-icon-file' : self::p('menu_icon');

        $data['menu_listsort'] = (int) self::p('menu_listsort');
        return $data;
    }

    /**
     * 添加模型的菜单
     * @param type $name 菜单语言键值
     * @param type $pid 菜单父类ID
     * @param type $url 菜单URL
     * @return type 返回插入结果
     */
    public static function insertModelMenu($name, $pid, $url) {
        return self::db('menu')->insert(array('menu_name' => $name, 'menu_pid' => $pid, 'menu_icon' => 'am-icon-file', 'menu_url' => $url));
    }

    /**
     * 删除菜单
     */
    public static function deleteMenu($menuName) {
        return self::db('menu')->where('menu_name = :menu_name')->delete(array('menu_name' => strtoupper($menuName)));
    }

}
