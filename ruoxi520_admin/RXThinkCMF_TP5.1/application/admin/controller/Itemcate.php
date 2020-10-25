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

use app\admin\service\ItemCateService;
use app\common\controller\Backend;

/**
 * 栏目管理-控制器
 * @author 牧羊人
 * @since 2020/7/10
 * Class Itemcate
 * @package app\admin\controller
 */
class Itemcate extends Backend
{
    /**
     * 初始化方法
     * @author 牧羊人
     * @date 2019/5/5
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\ItemCate();
        $this->service = new ItemCateService();
        $this->validate = new \app\admin\validate\ItemCate();
    }
}
