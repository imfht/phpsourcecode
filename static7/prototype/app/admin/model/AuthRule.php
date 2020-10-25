<?php

namespace app\admin\model;

use think\Model;

/**
 * Description of AuthRule
 * 权限规则
 * @author static7
 */
class AuthRule extends Model {
    /**
     * 更新和删除节点
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */

    public function ruleList(): array {
        //status全部取出,以进行更新
        $map = [
            'module' => 'admin',
            'type' => ['in', '1,2']
        ];
        $object = $this::all(function($query)use($map) {
                    $query->where($map)->order('name asc');
                });
        return $object ? object_to_array($object) : [];
    }

    /**
     * 权限规则数组更新
     * @param array $data 更新的数组
     * @param array $ids 数组id
     * @author staitc7 <static7@qq.com>
     */

    public function arrayUpdate(array $data = [], array $ids = []) {
        if (empty($ids)) {
            $this::update($data);
        } else {
            $map = [
                'id' => ['in', $ids],
            ];
            $this::where($map)->update($data);
        }
    }

    /**
     * 条件查询权限
     * @param array $map 查询条件
     * @param boole|string $field 查询的字段
     * @author staitc7 <static7@qq.com>
     */

    public function mapList(array $map = [], $field = true) {
        $object = $this::all(function($query)use($map, $field) {
                    $query->where($map)->field($field);
                });
        return $object ? object_to_array($object) : null;
    }
    /**
     * 添加菜单
     * @param array $data 添加的数据
     * @author staitc7 <static7@qq.com>
     */

    public function menuAdd($data) {
        $this::create($data);
    }
}
