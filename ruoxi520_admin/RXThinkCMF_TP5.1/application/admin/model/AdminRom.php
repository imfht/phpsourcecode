<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\admin\model\Role as AdminRoleModel;
use app\common\model\BaseModel;

/**
 * 权限-模型
 * @author 牧羊人
 * @since 2020/7/10
 * Class AdminRom
 * @package app\admin\model
 */
class AdminRom extends BaseModel
{
    // 设置数据表名
    protected $name = 'admin_rom';

    /**
     * 获取权限列表
     * @param $adminId 人员ID
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @since: 2020/7/10
     * @author 牧羊人
     */
    public function getPermissionList($adminId)
    {
        $adminMod = new Admin();
        $adminInfo = $adminMod->where("id", $adminId)->find();

        $map1 = [];
        if ($adminInfo['role_ids']) {
            $map1 = [
                ['r.type', '=', 1],
                ['r.type_id', 'in', $adminInfo['role_ids']],
            ];
        }
        $map2 = [
            ['r.type', '=', 2],
            ['r.type_id', '=', $adminId],
        ];
        $map = [$map1, $map2];
        $menuMod = new Menu();
        $menuList = $menuMod->alias('m')
            ->join(DB_PREFIX . 'admin_rom r', 'r.menu_id=m.id')
            ->distinct(true)
            ->where(function ($query) use ($map) {
                $query->whereOr($map);
            })
            ->where('m.type', '=', 3)
            ->where('m.status', '=', 1)
            ->where('m.mark', '=', 1)
            ->order('m.pid ASC,m.sort ASC')
            ->field('m.id')
            ->select();
        $list = [];
        if (!empty($menuList)) {
            foreach ($menuList as $vm) {
                // 根据菜单获取节点
                $funcList = $menuMod->alias('m')
                    ->join(DB_PREFIX . 'admin_rom r', 'r.menu_id=m.id')
                    ->distinct(true)
                    ->where(function ($query) use ($map) {
                        $query->whereOr($map);
                    })
                    ->where('m.type', '=', 4)
                    ->where('m.pid', '=', intval($vm['id']))
                    ->where('m.status', '=', 1)
                    ->where('m.mark', '=', 1)
                    ->order('m.id ASC')
                    ->field('m.id')
                    ->select();
                if ($funcList) {
                    foreach ($funcList as $v) {
                        $list[$vm['id']][] = $v['id'];
                    }
                }
            }
        }
        return $list;
    }
}
