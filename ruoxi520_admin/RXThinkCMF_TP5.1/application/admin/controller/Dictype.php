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

use app\admin\service\DicTypeService;
use app\common\controller\Backend;

/**
 * 字典类型-控制器
 * @author 牧羊人
 * @since 2020/7/10
 * Class Dictype
 * @package app\admin\controller
 */
class Dictype extends Backend
{
    /**
     * 初始化
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\DicType();
        $this->service = new DicTypeService();
        $this->validate = new \app\admin\validate\DicType();
    }
}
