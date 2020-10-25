<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: wdx(wdx@ourstu.com)
 * Date: 2018/10/15
 * Time: 13:06
 * ----------------------------------------------------------------------
 */

namespace app\admin\model;

use think\model;

/**
 * Class IPBlackList
 * IP黑名单
 * @package app\admin\model
 */
class IPBlackList extends Model
{
    protected $table = COMMON . 'ip_black_list';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}