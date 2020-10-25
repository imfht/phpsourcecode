<?php
//开启Session
session_start();
//设置时区
date_default_timezone_set("Asia/Shanghai");
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
function Get_Config($name)
{
    $redis = Redis_Link();
    $result = $redis->get('Config_' . $name);
    if (empty($result)) {
        $db_link = DB_Link();
        $row_config = mysqli_fetch_array(mysqli_query($db_link, "SELECT * FROM setting WHERE name = '" . $name . "'"));
        if (empty($row_config)) {
            return "";
        } else {
            $redis->set('Config_' . $name, $row_config['data']);
            return $row_config['data'];
        }
    } else {
        return $result;
    }
}
//登录状态检查
function Login_Status(){
    if ($_SESSION['login_status']==1){
        return true;
    }else{
        return false;
    }
}
//获取系统负载
class SystemInfoWindows
{
    /**
     * 判断指定路径下指定文件是否存在，如不存在则创建
     * @param string $fileName 文件名
     * @param string $content 文件内容
     * @return string 返回文件路径
     */
    private function getFilePath($fileName, $content)
    {
        $path = dirname(__FILE__) . "\\$fileName";
        if (!file_exists($path)) {
            file_put_contents($path, $content);
        }
        return $path;
    }
    /**
     * 获得cpu使用率vbs文件生成函数
     * @return string 返回vbs文件路径
     */
    private function getCupUsageVbsPath()
    {
        return $this->getFilePath(
            'cpu_usage.vbs',
            "On Error Resume Next
    Set objProc = GetObject(\"winmgmts:\\\\.\\root\cimv2:win32_processor='cpu0'\")
    WScript.Echo(objProc.LoadPercentage)"
        );
    }
    /**
     * 获得总内存及可用物理内存JSON vbs文件生成函数
     * @return string 返回vbs文件路径
     */
    private function getMemoryUsageVbsPath()
    {
        return $this->getFilePath(
            'memory_usage.vbs',
            "On Error Resume Next
    Set objWMI = GetObject(\"winmgmts:\\\\.\\root\cimv2\")
    Set colOS = objWMI.InstancesOf(\"Win32_OperatingSystem\")
    For Each objOS in colOS
     Wscript.Echo(\"{\"\"TotalVisibleMemorySize\"\":\" & objOS.TotalVisibleMemorySize & \",\"\"FreePhysicalMemory\"\":\" & objOS.FreePhysicalMemory & \"}\")
    Next"
        );
    }
    /**
     * 获得CPU使用率
     * @return Number
     */
    public function getCpuUsage()
    {
        $path = $this->getCupUsageVbsPath();
        exec("cscript -nologo $path", $usage);
        return $usage[0];
    }
    /**
     * 获得内存使用率数组
     * @return array
     */
    public function getMemoryUsage()
    {
        $path = $this->getMemoryUsageVbsPath();
        exec("cscript -nologo $path", $usage);
        $memory = json_decode($usage[0], true);
        $memory['usage'] = Round((($memory['TotalVisibleMemorySize'] - $memory['FreePhysicalMemory']) / $memory['TotalVisibleMemorySize']) * 100);
        return $memory;
    }
}
//API鉴权
function API_Auth($get,$post){
    if (empty($get)&&empty($post)){
        return false;
    }elseif(empty($get)){
        $api_key=$post;
    }else{
        $api_key=$get;
    }
    $real_api_key=Get_Config('api_key');
    if ($api_key==$real_api_key){
        return true;
    }else{
        return false;
    }
}
//删除目录及文件
function Delete_Dir($dirName)
{
    if(! is_dir($dirName))
    {
        return false;
    }
    $handle = @opendir($dirName);
    while(($file = @readdir($handle)) !== false)
    {
        if($file != '.' && $file != '..')
        {
            $dir = $dirName . '/' . $file;
            is_dir($dir) ? Delete_Dir($dir) : @unlink($dir);
        }
    }
    closedir($handle);

    return rmdir($dirName) ;
}
//更改系统设置
function Change_Config($name,$value){
    $db_link=DB_Link();
    $redis=Redis_Link();
    $row_config=mysqli_fetch_array(mysqli_query($db_link,"SELECT * FROM `setting` WHERE `name` = '".$name."'"));
    if (empty($row_config['ID'])){
        return false;
    }else{
        mysqli_query($db_link,"UPDATE `setting` SET `data` = '".$value."' WHERE `name` = '".$name."'");
        $redis->del('Config_'.$name);
        return true;
    }
}
//获取毫秒
function Get_ms() {
    list($msec, $sec) = explode(' ', microtime());
    $msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    return $msectime;
}
