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

use app\admin\service\AdService;
use app\common\controller\Backend;

/**
 * 广告管理-控制器
 * @author 牧羊人
 * @since 2020/7/10
 * Class Ad
 * @package app\admin\controller
 */
class Ad extends Backend
{
    /**
     * 初始化方法
     * @author 牧羊人
     * @date 2019/5/6
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\Ad();
        $this->service = new AdService();
        $this->validate = new \app\admin\validate\Ad();
    }
//
//    /**
//     * 添加或编辑
//     * @return mixed
//     * @throws \think\db\exception\DataNotFoundException
//     * @throws \think\db\exception\ModelNotFoundException
//     * @throws \think\exception\DbException
//     * @author 牧羊人
//     * @date 2019/5/6
//     */
//    public function edit()
//    {
//        // 获取广告位
//        $ad_sort_model = new AdSort();
//        $sort_list = $ad_sort_model->where(['mark' => 1])->select();
//        $this->assign('sort_list', $sort_list ? $sort_list->toArray() : []);
//
//        return parent::edit([
//            'type' => 1,
//        ]);
//    }
}
