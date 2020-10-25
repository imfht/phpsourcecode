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
 * 站点-模型
 * @author 牧羊人
 * @since 2020/7/10
 * Class Item
 * @package app\admin\model
 */
class Item extends BaseModel
{
    // 设置数据表名
    protected $name = 'item';

    /**
     * 获取缓存信息
     * @param int $id
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
            // 站点图片
            if ($info['image']) {
                $info['image_url'] = get_image_url($info['image']);
            }

            // 站点类型
            if ($info['type']) {
                $info['type_name'] = config('admin.item_type')[$info['type']];
            }
        }
        return $info;
    }
}
