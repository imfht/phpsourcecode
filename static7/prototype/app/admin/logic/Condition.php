<?php

namespace app\admin\logic;

use think\Request;

/**
 * Description of Condition
 * 查询条件 转化
 * @author static7
 */
class Condition {

    /**
     * 条件转化
     * @param array $array 数组
     * @author staitc7 <static7@qq.com>
     * @return mixed   
     */
    public function transform(array $array = []): array {
        if (empty($array)) {
            return [];
        }
        $where = [];
        foreach ($array as $k => &$v) {
            switch ($k) {
                case 'title':
                    $array[$k] && $where[$k] = ['like', "%{$array[$k]}%"];
                    break;
                case 'status':
                    ($array[$k] != "") && $where[$k] = $array[$k];
                    break;
                case 'create_time':
                    $create_time = $this->timeBetween($array[$k]);
                    $create_time ? $where[$k] = $create_time : null;
                    break;
                case 'update_time':
                    $update_time = $this->timeBetween($array[$k]);
                    $update_time ? $where[$k] = $update_time : null;
                    break;
                default:
                    break;
            }
        }
        return $where ? $where : [];
    }

    /**
     * 时间区间判断
     * @param array $time_between 时间区间
     * @author staitc7 <static7@qq.com>
     * @return mixed   
     */
    private function timeBetween(array $time_between = []) {
        if ($time_between[0] != '' && $time_between[1] != '') {
            if ($time_between[0] === $time_between[1]) {
                $map = ['between', [strtotime($time_between[0]), strtotime($time_between[0]) + 3600 * 24 - 1]];
            } elseif ($time_between[1] == date('Y-m-d')) {
                $map = ['between', [strtotime($time_between[0]), Request::instance()->time()]];
            }
            $map = ['between', [strtotime($time_between[0]), strtotime($time_between[1]) + 3600 * 24 - 1]];
        } elseif ($time_between[0] != '') {
            $map = ['egt', strtotime($time_between[0])];
        } elseif ($time_between[1] != '') {
            $map = ['elt', strtotime($time_between[1]) + 3600 * 24 - 1];
        }
        return $map ?? false;
    }

}
