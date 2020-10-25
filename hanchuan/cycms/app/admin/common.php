<?php
/**
*
* 函数：日志记录
* @param  string $log   日志内容。
* @param  string $name （可选）用户名。
*
**/
function addlog($log, $username=false)
{
    if ($username) {
        \think\facade\Db::name('log')->insert(['username'=>$username,'log'=>$log,'ip'=>$_SERVER["REMOTE_ADDR"],'t'=>time()]);
    }
}
