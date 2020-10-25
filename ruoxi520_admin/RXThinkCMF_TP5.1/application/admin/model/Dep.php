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
 * 部门-模型
 * @author 牧羊人
 * @since 2020/7/10
 * Class Dep
 * @package app\admin\model
 */
class Dep extends BaseModel
{
    // 设置数据表名
    protected $name = 'dep';

    /**
     * 获取子级部门
     * @param int $pid 上级ID
     * @param bool $flag 是否获取子级true或false
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function getChilds($pid = 0, $flag = false)
    {
        $list = [];
        $map = [
            'pid' => $pid,
            'mark' => 1,
        ];
        $result = $this->where($map)->order("sort asc")->select();
        if ($result) {
            foreach ($result as $val) {
                $id = (int)$val['id'];
                $info = $this->getInfo($id);
                if (!$info) {
                    continue;
                }
                if ($flag) {
                    $childList = $this->getChilds($id, 0);
                    $info['children'] = $childList;
                }
                $list[] = $info;
            }
        }
        return $list;
    }
}
