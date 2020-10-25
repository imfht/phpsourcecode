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

use app\common\model\BaseModel;

/**
 * 菜单-模型
 * @author 牧羊人
 * @since 2020/7/10
 * Class Menu
 * @package app\admin\model
 */
class Menu extends BaseModel
{
    // 设置数据表名
    protected $name = 'menu';

    /**
     * 获取缓存信息
     * @param int $id 记录ID
     * @return \app\common\model\数据信息|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @since 2020/7/10
     * @author 牧羊人
     */
    public function getInfo($id)
    {
        $info = parent::getInfo($id);
        if ($info) {
            // 菜单类型
            if ($info['type']) {
                $info['type_name'] = config('admin.menu_type')[$info['type']];
            }
        }
        return $info;
    }

    /**
     * 获取子级菜单
     * @param int $pid 上级ID
     * @param bool $isMenu 是否获取菜单
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function getChilds($pid = 0, $isMenu = true)
    {
        $map = [
            'pid' => $pid,
            'mark' => 1,
        ];
        $result = $this->where($map)->order("sort asc")->select();
        $list = [];
        if ($result) {
            foreach ($result as $val) {
                $id = (int)$val['id'];
                $info = $this->getInfo($id);
                if (!$info) {
                    continue;
                }
                $info['title'] = $info['name'];
                $info['font'] = "larry-icon";
                $itemList = $this->getChilds($id, $isMenu);
                $itemList = is_array($itemList) ? $itemList : [];
                if ($info['type'] == 3) {
                    if ($isMenu) {
                        $info['children'] = $itemList;
                    } else {
                        $info['funcList'] = $itemList;
                    }
                } else {
                    $info['children'] = $itemList;
                }
                $list[] = $info;
            }
        }
        return $list;
    }
}
