<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: wdx(wdx@ourstu.com)
 * Date: 2018/9/26
 * Time: 15:45
 * ----------------------------------------------------------------------
 */
namespace app\admin\behavior;

class UserLog
{
    public function run($params)
    {
        if (is_user_login()) {
            \app\admin\model\UserLog::record($params);
        }
    }
}