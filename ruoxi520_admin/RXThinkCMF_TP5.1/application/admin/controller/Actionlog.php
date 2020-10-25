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

use app\admin\model\ActionLog as ActionLogModel;
use app\admin\service\ActionLogService;
use app\admin\validate\ActionLog as ActionLogValidate;
use app\common\controller\Backend;

/**
 * 行为日志-控制器
 * @author 牧羊人
 * @since 2020/7/10
 * Class Actionlog
 * @package app\admin\controller
 */
class Actionlog extends Backend
{
    /**
     * 初始化
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\ActionLog();
        $this->service = new ActionLogService();
        $this->validate = new \app\admin\validate\ActionLog();
    }
}
