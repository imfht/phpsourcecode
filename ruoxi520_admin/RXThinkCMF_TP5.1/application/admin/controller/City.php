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

use app\admin\service\CityService;
use app\common\controller\Backend;

/**
 * 城市管理-控制器
 * @author 牧羊人
 * @since 2020/7/10
 * Class City
 * @package app\admin\controller
 */
class City extends Backend
{
    /**
     * 初始化
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\City();
        $this->service = new CityService();
        $this->validate = new \app\admin\validate\City();
    }

    /**
     * 获取子级城市【组件调用】
     * @return array
     * @since 2020/7/10
     * @author 牧羊人
     */
    public function getChilds()
    {
        if (IS_POST) {
            $id = input("post.id", 0);
            $list = $this->model->getChilds($id);
            return message('操作成功', true, $list);
        }
    }
}
