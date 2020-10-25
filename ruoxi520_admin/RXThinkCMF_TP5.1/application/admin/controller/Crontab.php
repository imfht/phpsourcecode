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

use app\admin\service\CrontabService;
use app\common\controller\Backend;

/**
 * 定时任务-控制器
 * @author 牧羊人
 * @since 2020/7/10
 * Class Crontab
 * @package app\admin\controller
 */
class Crontab extends Backend
{
    /**
     * 初始化
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\Crontab();
        $this->service = new CrontabService();
        $this->validate = new \app\admin\validate\Crontab();
    }
}
