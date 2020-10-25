<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: lin(lt@ourstu.com)
 * Date: 2018/9/11
 * Time: 14:06
 * ----------------------------------------------------------------------
 */

namespace app\common\model;

use think\Model;

class Adv extends Model
{
    protected $table = COMMON . 'adv';

    public function getStatusTextAttr($value, $data)
    {
        $status = [-1 => '删除', 0 => '禁用', 1 => '正常', 2 => '待审核'];
        return $status[$data['status']];
    }

    /**
     * @param $name
     * @param $path
     * @return array|null|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author:lin(lt@ourstu.com)
     */
    public function getAdvList($name, $path)
    {
        $data = cache('adv_list_' . $name . $path);
        if (empty($data)) {
            //$now_theme = modC('NOW_THEME', 'default', 'Theme');
            $advPosModel = new AdvPos;
            $advPos = $advPosModel->getInfo($name, $path); //找到当前调用的广告位
            //if ($advPos['theme'] != 'all' && !in_array($now_theme, explode(',', $advPos['theme']))) {
            //return null;
            //}
            $advMap[] = ['pos_id', '=', $advPos['id']];
            $advMap[] = ['status', '=', 1];
            $advMap[] = ['start_time', '<', time()];
            $advMap[] = ['end_time', '>', time()];
            $data = $this->where($advMap)->order('sort asc')->select()->toArray();
            foreach ($data as &$v) {
                $d = json_decode($v['data'], true);
                if (!empty($d)) {
                    $v = array_merge($d, $v);

                }
            }
            unset($v);
            cache('adv_list_' . $name . $path, $data);
        }
        return $data;
    }
}