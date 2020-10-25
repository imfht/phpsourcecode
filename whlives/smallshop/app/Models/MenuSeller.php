<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/4/14
 * Time: 上午10:03
 */

namespace App\Models;

/**
 * 商家菜单
 * Class Menu
 * @package App\Models
 */
class MenuSeller extends BaseModels
{
    //状态
    const STATUS_ON = 1;
    const STATUS_OFF = 0;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '锁定'
    ];

    protected $table = 'menu_seller';
    protected $guarded = ['id'];

    /**
     * 获取指定上级id下的所有菜单，按上下级排列(获取管理菜单)
     * @param int $parent_id 上级id
     * @return array
     */
    public static function getMenu($parent_id = 0) {
        $where = array(
            'parent_id' => $parent_id,
            'status' => self::STATUS_ON,
        );
        $result = self::select('id', 'title', 'icon', 'url')
            ->where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        $return_list = array();
        if (!$result->isEmpty()) {
            foreach ($result as $key => $value) {
                $_item = array(
                    'id' => $value['id'],
                    'title' => $value['title'],
                    'icon' => $value['icon'],
                    'name' => $value['url'],
                );
                $child = self::getMenu($value['id']);
                if ($child) {
                    $_item['list'] = $child;
                }
                $return_list[] = $_item;
            }
        }
        return $return_list;
    }

    /**
     * 获取指定上级id下的所有菜单，按上下级排列（后台管理）
     * @param int $parent_id 上级id
     * @return array
     */
    public static function getAll($parent_id = 0) {
        $where = array(
            'parent_id' => $parent_id,
        );
        $result = self::select('id', 'title', 'url', 'position', 'status')
            ->where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        $return_list = array();
        if (!$result->isEmpty()) {
            foreach ($result->toArray() as $key => $value) {
                $_item = $value;
                $child = self::getAll($value['id']);
                if ($child) {
                    $_item['children'] = $child;
                }
                $return_list[] = $_item;
            }
        }
        return $return_list;
    }

    /**
     * 获取指定上级id下的所有分类，按上下级排列（下拉框用）
     * @param int $parent_id 上级id
     * @param bool $is_children 是否需要下级
     * @return array
     */
    public static function getSelect($parent_id = 0, $is_children = false) {
        $where = array(
            'status' => self::STATUS_ON,
            'parent_id' => $parent_id,
        );
        $result = self::select('id', 'title')
            ->where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        $return_list = array();
        if (!$result->isEmpty()) {
            foreach ($result->toArray() as $key => $value) {
                $_item = $value;
                if ($is_children) {
                    $child = self::getSelect($value['id']);
                    if ($child) {
                        $_item['children'] = $child;
                    }
                }
                $return_list[] = $_item;
            }
        }
        return $return_list;
    }
}
