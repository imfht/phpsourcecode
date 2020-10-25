<?php

/**
 *  日志系统类
 *
 * @author: moxiaobai
 * @since : 2015/5/7 12:06
 */

namespace Service;

use Library\Db\MongoDb;

class Logger {

    /**
     * 定时器运行日志
     *
     * @param $data
     */
    public static function addTimerLog($data) {
        $instance = MongoDb::instance('cron');

        $instance->setCollection('taskRunLog');
        $instance->insert($data);
    }

}