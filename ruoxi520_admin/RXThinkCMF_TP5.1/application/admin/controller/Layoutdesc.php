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

namespace app\admin\controller;

use app\admin\service\LayoutDescService;
use app\common\controller\Backend;

/**
 * 布局描述-控制器
 * @author 牧羊人
 * @since 2020/7/10
 * Class Layoutdesc
 * @package app\admin\controller
 */
class Layoutdesc extends Backend
{
    /**
     * 初始化
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\LayoutDesc();
        $this->service = new LayoutDescService();
        $this->validate = new \app\admin\validate\LayoutDesc();
    }

    /**
     * 根据站点ID获取描述列表
     * @return array
     * @since 2020/7/10
     * @author 牧羊人
     */
    public function getLayoutDescList()
    {
        if (IS_POST) {
            // 站点ID
            $itemId = request()->param("item_id", 0);
            $list = $this->model->where(['item_id' => $itemId, 'mark' => 1])->order("sort asc")->select();
            return message("操作成功", true, $list);
        }
    }
}
