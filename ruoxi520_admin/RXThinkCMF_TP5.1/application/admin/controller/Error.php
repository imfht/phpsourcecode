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

use app\common\controller\Backend;

/**
 * 错误页面-控制器
 * @author 牧羊人
 * @since 2020/7/11
 * Class Error
 * @package app\admin\controller
 */
class Error extends Backend
{
    /**
     * 错误页面入口
     * @return mixed
     * @since 2020/7/11
     * @author 牧羊人
     */
    public function index()
    {
        return $this->render("public/404");
    }
}
