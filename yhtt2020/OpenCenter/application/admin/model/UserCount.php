<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: wdx(wdx@ourstu.com)
 * Date: 2018/9/12
 * Time: 16:05
 * ----------------------------------------------------------------------
 */

namespace app\admin\model;

use think\Model;

class UserCount extends Model
{
    protected $table = USER . 'count';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * 更新用户积分
     * @param int $uid
     * @param int $type
     * @param int $num
     * @return bool
     * @author:wdx(wdx@ourstu.com)
     */
    public function updateScore($uid = 0, $type = 0, $num = 0)
    {
        $rs = $this->where('uid', $uid)->setField('score' . $type, $num);
        if ($rs) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取用户积分
     * @param int $id
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author:wdx(wdx@ourstu.com)
     */
    public function getScoreCount($id = 0)
    {
        $scoreType = model('admin/ScoreType')->where('status', 1)->column('id');
        $field = [];
        foreach ($scoreType as $val) {
            $field[] = 'score' . $val;
        }
        $data = $this->where('uid', $id)->field($field)->find();
        return $data;
    }
}