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

use app\admin\service\LevelService;
use app\common\controller\Backend;

/**
 * 职级-控制器
 * @author 牧羊人
 * @since 2020/7/10
 * Class Level
 * @package app\admin\controller
 */
class Level extends Backend
{
    /**
     * 初始化
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\Level();
        $this->service = new LevelService();
        $this->validate = new \app\admin\validate\Level();
    }
}
