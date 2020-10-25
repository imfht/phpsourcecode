<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

    namespace think;
    /**
     * Class Log
     * @package think
     */
    use log\Log as L;
    class Log
    {
        const LOG    = 'log';
        const ERROR  = 'error';
        const INFO   = 'info';
        const SQL    = 'sql';
        const NOTICE = 'notice';
        const ALERT  = 'alert';


        /**
         * 记录调试信息
         * @param mixed  $msg  调试信息
         * @param string $type 信息类型
         * @return void
         */
        public static function record($msg, $type = 'log')
        {
            $Module='think';//区分think打印的日志
            if($type == 'error'){
                L::error($msg,array(),$Module);
            }elseif($type == 'alert'){
                L::alert($msg,array(),$Module);
            }elseif($type == 'sql'){
                L::info($msg,array(),$Module);
            }else{
                L::notice($msg,array(),$Module);
            }
        }
    }
