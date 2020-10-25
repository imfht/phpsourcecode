<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

/**
 * 商家商品分类
 * Class Category
 * @package App\Models
 */
class SellerCategory extends BaseModels
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '锁定'
    ];

    const LOOP_LEVEL = 1;//最多层级

    protected $table = 'seller_category';
    protected $guarded = ['id'];

    /**
     * 获取指定上级id下的所有菜单，按上下级排列（后台管理）
     * @param int $seller_id 商家id
     * @param int $parent_id 上级id
     * @return array
     */
    public static function getAll($seller_id, $parent_id = 0) {
        $where = array(
            'seller_id' => $seller_id,
            'parent_id' => $parent_id,
        );
        $result = self::select('id', 'title', 'image', 'parent_id', 'position', 'status')
            ->where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        $return_list = array();
        if (!$result->isEmpty()) {
            foreach ($result->toArray() as $key => $value) {
                $_item = $value;
                $child = self::getAll($seller_id, $value['id']);
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
     * @param int $seller_id 商家id
     * @param int $parent_id 上级id
     * @param bool $is_children 是否需要下级
     * @return array
     */
    public static function getSelect($seller_id, $parent_id = 0, $is_children = false) {
        $where = array(
            'status' => self::STATUS_ON,
            'parent_id' => $parent_id,
            'seller_id' => $seller_id,
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
                    $child = self::getSelect($seller_id, $value['id'], $is_children);
                    if ($child) {
                        $_item['children'] = $child;
                    }
                }
                $return_list[] = $_item;
            }
        }
        return $return_list;
    }

    /**
     * 获取指定id下的所有分类id
     * @param int $seller_id 商家id
     * @param int $parent_id 上级id
     * @param bool $is_children 是否需要下级
     * @return array
     */
    public static function getChildId($seller_id, $parent_id = 0, $is_children = true) {
        $return_list[] = $parent_id;
        $where = array(
            'status' => self::STATUS_ON,
            'parent_id' => $parent_id,
            'seller_id' => $seller_id,
        );
        $result = self::where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->pluck('id')
            ->toArray();
        if ($result) {
            $return_list = array_merge($return_list, $result);
            foreach ($result as $value) {
                if ($is_children) {
                    $child = self::getChildId($seller_id, $value, $is_children);
                    if ($child) {
                        $return_list = array_merge($return_list, $child);
                    }
                }
            }

        }
        return array_unique($return_list);
    }
}
