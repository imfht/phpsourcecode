<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: wdx(wdx@ourstu.com)
 * Date: 2018/10/17
 * Time: 19:45
 * ----------------------------------------------------------------------
 */
namespace app\admin\model;

use think\model;

class ScoreLog extends model
{
    protected $table = USER . 'score_log';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function getList($map, $page = 1, $row = 20)
    {
        $data = $this->where($map)->page($page, $row)->select();
        return $data;
    }


    public function checkScoreLog($scoreRule = [])
    {
        $uid = get_uid();
        switch ($scoreRule['time_unit']) {
            case 1:
                $back = 1;
                break;
            case 2:
                $back = 60;
                break;
            case 3:
                $back = 3600;
                break;
            case 4:
                $back = 86400;
                break;
            case 5:
                $back = 604800;
                break;
            case 6:
                $back = 2592000;
                break;
            case 7:
                $back = 31536000;
                break;
        }
        $time = time() - $back;
        $count = $this
            ->where('uid', $uid)
            ->where('rule_id', $scoreRule['rule_id'])
            ->where('create_time', '>', $time)
            ->count();
        if ($count >= $scoreRule['frequency']) {
            return false;
        } else {
            return true;
        }
    }
}