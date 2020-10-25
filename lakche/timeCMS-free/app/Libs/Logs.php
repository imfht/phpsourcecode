<?php
/**
 * Created by Joy.
 * User: Joy
 */
namespace App\Libs;

use App\Model\Log;
use Request;
use Auth;

/**日志系统
 */

class Logs
{
    public static function save($module, $module_id, $operation, $info)
    {
        $ip = Request::getClientIp();
        if(auth()->check()){
            $user_id = Auth::user()->id;
        } else {
            $user_id = 0;
        }
        $log = new Log;
        $log->user_id = $user_id;
        $log->module = $module;
        $log->module_id = $module_id;
        $log->operation = $operation;
        $log->info = $info;
        $log->ip = $ip;
        $log->save();
    }
}