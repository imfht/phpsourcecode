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

use app\admin\model\Ad;
use app\common\service\BaseService;

/**
 * 广告管理-服务类
 * @author 牧羊人
 * @since 2020/7/10
 * Class AdService
 * @package app\admin\service
 */
class AdService extends BaseService
{
    /**
     * 初始化
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new Ad();
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

        // 图片处理
        $cover = trim($data['cover']);
        if (strpos($cover, "temp")) {
            $data['cover'] = save_image($cover, 'ad');
        }

        return parent::edit($data);
    }
}
