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

namespace app\admin\service;

use app\admin\model\Layout;
use app\common\service\BaseService;

/**
 * 布局管理-服务类
 * @author 牧羊人
 * @since 2020/7/10
 * Class LayoutService
 * @package app\admin\service
 */
class LayoutService extends BaseService
{
    /**
     * 初始化
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new Layout();
    }

    /**
     * 添加或编辑
     * @return array
     * @since 2020/7/10
     * @author 牧羊人
     */
    public function edit()
    {
        // 参数
        $data = request()->param();
        // 图片
        $image = trim($data['image']);
        // 图片校验
        if (!$data['id'] && !$image) {
            return message('请上传封面', false);
        }
        if (strpos($image, "temp")) {
            $data['image'] = save_image($image, 'layout');
        }
        return parent::edit($data);
    }
}
