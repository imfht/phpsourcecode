<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2017/1/3
 * Time: 21:34
 */

/** 
 * 在此定义任务列表（数组）
 * 形式为 任务名=>函数
 * 每一个任务将会有一个对应的存储空间，通过读写这个存储空间来保存状态
 * 读写存储空间将简单的通过 传入参数-处理参数-返回结果 来实现
 */
return [
    //清理任务，自动清理掉过期的缓存和太久远的日志
    'cleaner'=>function($data){
        $lastTime=Yuri2::arrGetSet($data,'lastTime');
        if ($lastTime+1200<TIMESTAMP){
            //最少间隔n秒会执行

            //清理缓存
            Yuri2::arrGetSet($data,'lastTime',TIMESTAMP);
            \naples\lib\Factory::getCache()->cleanOverTime();

            //清理日志
            $dirLogs=PATH_RUNTIME.'/logs';
            if (is_dir($dirLogs)){
                \Yuri2::ergodicDir($dirLogs,function ($file) use(&$dirLogs){
                    $fileFullPath=$dirLogs.'/'.$file;
                    $mtime=filemtime($fileFullPath);
                    if ($mtime+31104000<TIMESTAMP){
                        unlink($fileFullPath);
                    }
                });
            }

            //清理临时
            Yuri2::delDir(PATH_RUNTIME.'/temp',true);
        }
        return $data;
    },
    //检查 白名单 黑名单
    'accessList'=>function($data){

        function accessForbidden(){
            config('debug',false);//关闭debug
            config('turn_off_error_display',true);//关闭原版错误提示
            fastLog('此次访问因为不符合访问许可而被拦截','访问被拦截',2);
            error('您暂时不能访问此站点。如有疑问，请联系管理员。');
        }

        $dbList=\naples\lib\Factory::getArrDatabase('sys/accessList');
        $mode=Yuri2::arrGetSet($dbList->data,'mode');
        if ($mode=='free'){return $data;} //不限制
        $ip=Yuri2::getIp();
        switch ($mode){
            case 'white':
                $whiteList=\Yuri2::arrGetSet($dbList->data,'whiteList');
                if (isset($whiteList[$ip])){
                    $exp=$whiteList[$ip]['exp'];
                    if ($exp>TIMESTAMP){
                        //有效
                        return $data;
                    }else{
                        //过期
                        unset($dbList->data['whiteList'][$ip]);
                        $dbList->save();
                        accessForbidden();
                        return $data;
                    }
                }else{
                    accessForbidden();
                    return $data;
                }
                break;
            case 'black':
                $blackList=\Yuri2::arrGetSet($dbList->data,'blackList');
                if (isset($blackList[$ip])){
                    $exp=$blackList[$ip]['exp'];
                    if ($exp>TIMESTAMP){
                        //有效
                        accessForbidden();
                        return $data;
                    }else{
                        //过期
                        unset($dbList->data['blackList'][$ip]);
                        $dbList->save();
                        return $data;
                    }
                }else{
                    return $data;
                }
                break;
        }
        return $data;
    }
];