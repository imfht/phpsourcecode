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
 * 栏目-模型
 * @author 牧羊人
 * @since 2020/7/10
 * Class ItemCate
 * @package app\admin\model
 */
class ItemCate extends BaseModel
{
    // 设置数据表名
    protected $name = 'item_cate';

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
        $info = parent::getInfo($id, true);
        if ($info) {
            // 栏目封面
            if ($info['cover']) {
                $info['cover_url'] = get_image_url($info['cover']);
            }

            // 上级栏目
            if ($info['pid']) {
                $parent_info = $this->getInfo($info['pid']);
                $info['parent_name'] = $parent_info['name'];
            }

            // 获取站点
            if ($info['item_id']) {
                $item_model = new Item();
                $item_info = $item_model->getInfo($info['item_id']);
                $info['item_name'] = $item_info['name'];
            }
        }
        return $info;
    }

    /**
     * 根据站点获取栏目列表
     * @param int $itemId 站点ID
     * @param int $pid 上级栏目ID
     * @param bool $flag 是否获取子级
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @since 2020/7/2
     * @author 牧羊人
     */
    public function getChilds($itemId = 0, $pid = 0, $flag = false)
    {
        $map = [
            'pid' => $pid,
            'mark' => 1,
        ];
        if ($itemId) {
            $map['item_id'] = $itemId;
        }
        $list = [];
        $result = $this->where($map)->order("sort asc")->select();
        if ($result) {
            foreach ($result as $val) {
                $info = $this->getInfo($val['id']);
                if (!$info) {
                    continue;
                }
                if ($flag) {
                    $childList = $this->getChilds($itemId, $val['id'], 0);
                    $info['children'] = $childList;
                }
                $list[] = $info;
            }
        }
        return $list;
    }

    /**
     * 获取栏目名称
     * @param $cateId 栏目ID
     * @param string $delimiter 拼接字符(如：">>")
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @since 2020/7/2
     * @author 牧羊人
     */
    public function getCateName($cateId, $delimiter = "")
    {
        $names = [];
        do {
            $info = $this->getInfo($cateId);
            $names[] = isset($info['name']) ? $info['name'] : '';
            $cateId = isset($info['pid']) ? $info['pid'] : 0;
        } while ($cateId > 0);
        if (!empty($names)) {
            $names = array_reverse($names);
            if (strpos($names[1], $names[0]) === 0) {
                unset($names[0]);
            }
            return implode($delimiter, $names);
        }
        return null;
    }
}
