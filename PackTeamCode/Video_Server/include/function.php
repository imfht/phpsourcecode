<?php
//设置时区
date_default_timezone_set("Asia/Shanghai");
function getDir($dir)
{
    $dirArray[] = NULL;
    if (false != ($handle = opendir($dir))) {
        $i = 0;
        while (false !== ($file = readdir($handle))) {
            //去掉"“.”、“..”以及带“.xxx”后缀的文件
            if ($file != "." && $file != ".." && !strpos($file, ".")) {
                $dirArray[$i] = $file;
                $i++;
            }
        }
        closedir($handle);
    }
    return $dirArray;
}

function getFile($dir)
{
    $fileArray[] = NULL;
    if (false != ($handle = opendir($dir))) {
        $i = 0;
        while (false !== ($file = readdir($handle))) {
            //去掉"“.”、“..”以及带“.xxx”后缀的文件
            if ($file != "." && $file != ".." && strpos($file, ".")) {
                $fileArray[$i] = $file;
                if ($i == 100) {
                    break;
                }
                $i++;
            }
        }
        closedir($handle);
    }
    return $fileArray;
}

function Random_String($length)
{
    $str = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
    shuffle($str);
    $str = implode('', array_slice($str, 0, $length));
    return $str;
}

//Redis
function Redis_Link()
{
    global $redis_address;
    global $redis_port;
    global $redis_auth;
    $redis = new Redis();
    $redis->pconnect($redis_address, $redis_port);
    $redis->auth($redis_auth);
    $redis->select(1);
    return $redis;
}

//Mysql
function DB_Link()
{
    global $mysql_address;
    global $mysql_port;
    global $mysql_username;
    global $mysql_password;
    global $mysql_db_name;
    $db_link = mysqli_connect($mysql_address, $mysql_username, $mysql_password, $mysql_db_name, $mysql_port);
    if (!$db_link) {
        echo "Mysql Error";
        exit;
    } else {
        mysqli_query($db_link, "SET NAMES utf8");
        return $db_link;
    }
}

//Get Config
//Get Config
function Get_Config($name)
{
    $redis = Redis_Link();
    $result = $redis->get('Config_' . $name);
    if (empty($result)) {
        $db_link = DB_Link();
        $row_config = mysqli_fetch_array(mysqli_query($db_link, "SELECT * FROM setting WHERE name = '" . $name . "'"));
        if (empty($row_config)) {
            return 0;
        } else {
            $redis->set('Config_' . $name, $row_config['data']);
            return $row_config['data'];
        }
    } else {
        return $result;
    }
}

//Output API
function Col_echo($text, $color)
{
    switch ($color) {
        case "black":
            $prefix = "\033[0;30m";
            break;
        case "dark_gray":
            $prefix = "\033[1;30m";
            break;
        case "blue":
            $prefix="\033[0;34m";
            break;
        case "light_blue":
            $prefix="\033[1;34m";
            break;
        case "green":
            $prefix="\033[0;32m";
            break;
        case "light_green":
            $prefix="\033[1;32m";
            break;
        case "cyan":
            $prefix="\033[0;36m";
            break;
        case "light_cyan":
            $prefix="\033[1;36m";
            break;
        case "red":
            $prefix="\033[0;31m";
            break;
        case "light_red":
            $prefix="\033[1;31m";
            break;
        case "purple":
            $prefix="\033[0;35m";
            break;
        case "light_purple":
            $prefix="\033[1;35m";
            break;
        case "brown":
            $prefix="\033[0;33m";
            break;
        case "yellow":
            $prefix="\033[1;33m";
            break;
        case "light_gray":
            $prefix="\033[0;37m";
            break;
        case "white":
            $prefix="\033[1;37m";
            break;
        default:
            $prefix="\033[0m";
            break;
    }
    echo $prefix.$text."\033[0m";
}
//获取视频信息
function Get_Video_Info($ffmpeg,$video){
    $cache_random=Random_String(8);
    exec($ffmpeg.' -i '.$video.' > cache_'.$cache_random.' 2>&1');
    $cache=file_get_contents('cache_'.$cache_random);
    unlink('cache_'.$cache_random);
    if (preg_match("/Duration: (.*?), start: (.*?), bitrate: (\d*) kb\/s/", $cache, $match)) {
        $return['duration'] = $match[1]; //播放时间
        $arr_duration = explode(':', $match[1]);
        $return['seconds'] = $arr_duration[0] * 3600 + $arr_duration[1] * 60 + $arr_duration[2]; //转换播放时间为秒数
        $return['start'] = $match[2]; //开始时间
        $return['bitrate'] = $match[3]; //码率(kb)
    }else{
        $return=false;
    }
    return $return;
}
