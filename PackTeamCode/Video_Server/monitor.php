<?php
echo "[Monitor] Starting.....\n";
exec('mode con cols=30 lines=14');
exec('title Monitor');
echo "[Monitor] Loading Function Database\n";
include("include/function.php");
include("config/config.php");
Col_echo("[Monitor] Connecting to Redis\n",'yellow');
$redis = Redis_Link();
Col_echo("[Monitor] Connecting to Mysql\n",'yellow');
$db_link = DB_Link();
//
//
//子线程监控
//
//
start:
exec('title ['.date('H:i:s',time()).']');
//动态加载配置
$worker_thread = Get_Config('worker_thread');
Col_echo("Worker Status Monitor \n",'brown');
for ($i = 1; $i <= $worker_thread; $i++) {
    $status = $redis->get('Worker_Status_' . $i);
    if (empty($status)){
        Col_echo("[".$i."] Free\n",'green');
    }else{
        $worker_step=$redis->get('Worker_Monitor_'.$i);
        if (empty($worker_step)){
            Col_echo("[".$i."] Worker Initialization\n",'yellow');
        }elseif ($worker_step==1){
            $cache = file_get_contents('cache\\worker_'.$i);
            if (empty($cache)){
                Col_echo("[".$i."] Read Cache Failed\n",'red');
            }else{
                preg_match("/Duration: (.*?), start: (.*?), bitrate: (\d*) kb\/s/", $cache, $match);
                $arr_duration = explode(':', $match[1]);
                $full_time = $arr_duration[0] * 3600 + $arr_duration[1] * 60 + $arr_duration[2];
                preg_match_all("/time\=(.*?) bitrate/", $cache, $match);
                $already_encode = $match[1][count($match[1]) - 1];
                $already_encode = explode(':', $already_encode);
                $already_encode = $already_encode[0] * 3600 + $already_encode[1] * 60 + $already_encode[2];
                $encode_per=round($already_encode/$full_time*100,2);
                Col_echo("[".$i."] Encoding (".$encode_per."%)\n",'light_green');
                $redis->set('Monitor_Per_'.$i,$encode_per);
            }
        }elseif ($worker_step==2){
            Col_echo("[".$i."] ScreenShot\n",'light_green');
        }elseif ($worker_step==3){
            Col_echo("[".$i."] Segmenting\n",'light_green');
        }else{
            Col_echo("[".$i."] Unknown\n",'red');
        }
    }
}
Col_echo("[".date('Y-m-d H:i:s',time())."]",'brown');
$cmd_end=14-$worker_thread-2;
for ($num=0;$num<$cmd_end;$num++){
    echo "\n";
}
sleep(1);
goto start;
