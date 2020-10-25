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

use app\admin\service\ConfigService;
use app\common\controller\Backend;

/**
 * 配置管理-控制器
 * @author 牧羊人
 * @since 2020/7/10
 * Class Config
 * @package app\admin\controller
 */
class Config extends Backend
{
    /**
     * 初始化
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\Config();
        $this->service = new ConfigService();
        $this->validate = new \app\admin\validate\Config();
    }

    /**
     * 获取数据列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function index()
    {
        // 配置分组ID
        $groupId = request()->param('group_id', 1);

        // 获取配置分组
        $configGroupMod = new \app\admin\model\ConfigGroup();
        $configGroupList = $configGroupMod->where(['mark' => 1])->select()->toArray();

        return parent::index([
            'group_id' => $groupId,
            'configGroupList' => $configGroupList,
        ]);
    }

    /**
     * 添加或编辑
     * @return mixed
     * @since 2020/7/10
     * @author 牧羊人
     */
    public function edit()
    {
        // 分组ID
        $groupId = request()->param('group_id', 0);
        return parent::edit([
            'group_id' => $groupId,
        ]);
    }
}
