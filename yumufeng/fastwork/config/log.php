<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/30
 * Time: 14:14
 */

return [
    // 日志记录级别，共8个级别
    'level' => ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG', 'SQL','SWOOLE'],
    /**
     * 多长时间保存一次日志， 默认3秒，先将日志保存到内存中，再异步写入文件提高性能
     */
    'save_time' => 3000,
];