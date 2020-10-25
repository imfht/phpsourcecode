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

namespace app\admin\model;

use app\common\model\BaseModel;

/**
 * 行为-模型
 * @author 牧羊人
 * @since 2020/7/10
 * Class Action
 * @package app\admin\model
 */
class Action extends BaseModel
{
    // 设置数据表名
    protected $name = 'action';

    /**
     * 获取缓存信息
     * @param int $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @date 2019/5/14
     */
    public function getInfo($id)
    {
        $info = parent::getInfo($id, true);
        if ($info) {
            // 来源类型
            if ($info['type']) {
                $info['type_name'] = config('admin.action_type')[$info['type']];
            }
            // 执行类型
            if ($info['execution']) {
                $info['execution_name'] = config("admin.action_execution")[$info['execution']];
            }
        }
        return $info;
    }
}
