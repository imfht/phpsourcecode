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
 * 城市-模型
 * @author 牧羊人
 * @since 2020/7/10
 * Class City
 * @package app\admin\model
 */
class City extends BaseModel
{
    // 设置数据表名
    protected $name = 'city';

    /**
     * 获取子级城市
     * @param $pid 上级ID
     * @param bool $flag 是否获取子级
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 牧羊人
     * @since: 2020/7/10
     */
    public function getChilds($pid, $flag = false)
    {
        $list = [];
        $result = $this->where([
            'pid' => $pid,
            'mark' => 1
        ])->order("id asc")->select()->toArray();
        if ($result) {
            foreach ($result as $val) {
                $id = (int)$val['id'];
                $info = $this->getInfo($id);
                if ($flag) {
                    $childList = $this->getChilds($id, $flag);
                    if (is_array($childList)) {
                        $info['children'] = $childList;
                    }
                }
                if ($flag) {
                    $list[] = $info;
                } else {
                    $list[$id] = $info;
                }
            }
        }
        return $list;
    }

    /**
     * 获取城市名称
     * @param $cityId 城市ID
     * @param string $delimiter 拼接字符串
     * @param bool $isReplace 是否替换关键词
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 牧羊人
     * @since: 2020/7/10
     */
    public function getCityName($cityId, $delimiter = "", $isReplace = false)
    {
        $names = [];
        do {
            $info = $this->getInfo($cityId);
            if ($info) {
                if ($isReplace) {
                    $names[] = str_replace(array("省", "市", "维吾尔", "壮族", "回族", "自治区"), "", $info['name']);
                } else {
                    $names[] = $info['name'];
                }
            }
            $cityId = isset($info['pid']) ? (int)$info['pid'] : 0;
        } while ($cityId > 1);
        if (!empty($names)) {
            $names = array_reverse($names);
            if (strpos($names[1], $names[0]) === 0) {
                unset($names[0]);
            }
            return implode($delimiter, $names);
        }
        return null;
    }
}
