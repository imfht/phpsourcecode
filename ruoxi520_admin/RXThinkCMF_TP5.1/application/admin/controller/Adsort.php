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

use app\admin\model\AdSort as AdSortModel;
use app\admin\service\AdSortService;
use app\admin\validate\AdSort as AdSortValidate;
use app\common\controller\Backend;

/**
 * 广告位管理-控制器
 * @author 牧羊人
 * @since 2020/7/10
 * Class Adsort
 * @package app\admin\controller
 */
class Adsort extends Backend
{
    /**
     * 初始化
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\AdSort();
        $this->service = new AdSortService();
        $this->validate = new \app\admin\validate\AdSort();
    }
}
