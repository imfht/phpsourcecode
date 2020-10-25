<?php
/**
 * 系统初始化
 */
namespace Common\Behavior;

use Think\Log;

class AppInitBehavior {
    public function run(&$params) {
        $logConfig = array(
            'type'              =>  C('LOG_TYPE'),
            'log_time_format'   =>  ' c ',
            'log_file_size'     =>  C('LOG_FILE_SIZE'),
            'log_path'          =>  LOG_PATH,
        );
        Log::init($logConfig);
    }
}