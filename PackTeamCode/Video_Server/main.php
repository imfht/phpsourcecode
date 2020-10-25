<?php
echo "Video Encode System\n";
echo "===================\n";
echo "Build By haha_Dashen\n";
echo "Base on PackPHPFrame\n";
echo "===================\n";
echo "Loading Function Database......";
include("include/function.php");
include("config/config.php");
exec('title Video Encode System [Build:'.$build.']');
//Color Text Support
Col_echo("[Successful]\n", 'green');
Col_echo("Connecting to Redis......", 'light_blue');
$redis = Redis_Link();
$redis->flushDB();
Col_echo("[Successful]\n", 'green');
Col_echo("Connecting to Mysql......", 'light_blue');
$db_link = DB_Link();
Col_echo("[Successful]\n", 'green');
Col_echo("[Main] Dump Setting\n", "brown");
$encode_bitrate_video = Get_Config('encode_bitrate_video');
$encode_bitrate_audio = Get_Config('encode_bitrate_audio');
$encode_ts_time = Get_Config('encode_ts_time');
$encode_ts_frame = Get_Config('encode_ts_frame');
$worker_thread = Get_Config('worker_thread');
if ($dual_socket_support == 1) {
    Col_echo("[Main] Dual Socket Support Enable!\n", "brown");
}
Col_echo("[MultiThread] Sleep" . "\n", "blue");
start:
Col_echo("[MultiThread] Time:" . date("Y-m-d H:i:s") . "\n", "brown");
//Dynamic Load Config
$encode_bitrate_video = Get_Config('encode_bitrate_video');
$encode_bitrate_audio = Get_Config('encode_bitrate_audio');
$encode_ts_time = Get_Config('encode_ts_time');
$encode_ts_frame = Get_Config('encode_ts_frame');
$worker_thread = Get_Config('worker_thread');
//Worker Info
$worker_free = 0;
$worker_busy = 0;
for ($i = 1; $i <= $worker_thread; $i++) {
    $status = $redis->get('Worker_Status_' . $i);
    if (empty($status)) {
        $worker_free++;
    } elseif ($status == "1"||$status=="2") {
        $worker_busy++;
    }
    $status = 0;
}
Col_echo("[MultiThread] Free:" . $worker_free . " Busy:" . $worker_busy . "\n", 'white');
//Scan File
$start_sign = $redis->get('Main_Start');
if ($start_sign == '1') {
    Col_echo("[File] Searching Encode File\n", 'cyan');
    $file = getFile("upload");
    if (empty($file[0])) {
        Col_echo("[File] No File Find\n", 'cyan');
    } else {
        for ($num = 0; !empty($file[$num]); $num++) {
            //MD5
            $md5 = md5_file('upload\\' . $file[$num]);
            $already = mysqli_fetch_array(mysqli_query($db_link, "SELECT * FROM video_list WHERE md5 = '" . $md5 . "'"));
            if (!empty($already['ID'])) {
                Col_echo("[File] MD5 Matched \n", 'green');
                //mysqli_query($db_link, "INSERT INTO `video_list` (`ID`, `filename`, `random`, `day`, `time`, `status`, `md5`) VALUES (NULL, '" . $file[$num] . "', '" . $already['random'] . "', '" . $already['day'] . "', '" . time() . "', '2', '" . $md5 . "')");
                unlink('upload\\' . $file[$num]);
            } else {
                $today = date("Ymd", time());
                $random = Random_String(8);
                mysqli_query($db_link, "INSERT INTO `video_list` (`ID`, `filename`, `random`, `day`, `time`, `status`, `md5`) VALUES (NULL, '" . $file[$num] . "', '" . $random . "', '" . $today . "', '" . time() . "', '0', '" . $md5 . "')");
                Col_echo("[File] Find 1 Encode File\n", 'light_blue');
                $file_type = end(explode(".", $file[$num]));
                //Move And Rename Encode File (Filename same as 'Random')
                rename('upload\\' . $file[$num], 'encoding\\' . $random . '.' . $file_type);
            }
        }
    }
    $redis->del('Main_Start');
}
//TaskManager
$result_waiting = mysqli_query($db_link, "SELECT * FROM video_list WHERE status = '0'");
Col_echo("[TaskManager] Start Push Task\n", 'purple');
$waiting = 0;
while ($row_waiting = mysqli_fetch_array($result_waiting)) {
    $find = 0;
    for ($i = 1; $i <= $worker_thread && $find == 0; $i++) {
        $status = $redis->get('Worker_Status_' . $i);
        if (empty($status)) {
            Col_echo("[TaskManager] Push Task To " . $i . "#\n", 'light_purple');
            Col_echo("[MultiThread] Pull Worker " . $i . "# UP\n", 'green');
            pclose(popen('start php\php.exe -c php\php.ini worker.php ' . $i . ' ' . $row_waiting['ID'], 'r'));
            mysqli_query($db_link, "UPDATE `video_list` SET `status` = '1' WHERE `ID` = " . $row_waiting['ID'] . ";");
            sleep(2);
            $find = 1;
        }
    }
    if ($find == 0) {
        $waiting++;
    }
}
Col_echo("[TaskManager] " . $waiting . " Task Waiting\n", 'purple');
sleep(5);
goto start;