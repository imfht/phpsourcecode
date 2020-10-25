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
 * 布局描述-模型
 * @author 牧羊人
 * @since 2020/7/10
 * Class LayoutDesc
 * @package app\admin\model
 */
class LayoutDesc extends BaseModel
{
    // 设置数据表名
    protected $name = 'layout_desc';

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
            // 获取站点信息
            if ($info['item_id']) {
                $itemModel = new Item();
                $item_info = $itemModel->getInfo($info['item_id']);
                $info['item_name'] = $item_info['name'];
            }
        }
        return $info;
    }
}
