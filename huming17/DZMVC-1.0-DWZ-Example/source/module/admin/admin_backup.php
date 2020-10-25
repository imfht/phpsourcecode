<?php

/**
 * 备份管理
 * @author HumingXu E-mail:huming17@126.com
 */
@set_time_limit(0);
@set_magic_quotes_runtime(0);
ini_set('memory_limit', '128M');
if(!is_dir(SITE_ROOT.'./data/backup')){
    dmkdir(SITE_ROOT.'./data/backup');
}
switch ($do) {
    case "delete":
        $backup_id = isset($_REQUEST['backup_id']) ? $_REQUEST['backup_id'] : '';
        if($backup_id){
            //DEBUG 删除目录
            $is_unix = is_unix();
            if($is_unix==1 && function_exists('shell_exec')){
                $backup_path = SITE_ROOT.'./data/backup';
                $cmd = 'rm -rf '.SITE_ROOT.'./data/backup/backup.'.$backup_id;
                shell_exec($cmd);
                //DEBUG 重新排序备份文件夹
                $rename_array = directoryToArray($backup_path, false, true, false);
                krsort($rename_array);
                foreach($rename_array AS $key => $value){
                    $cmd = 'mv '.$value.' '.SITE_ROOT.'./data/backup/backup.1'.($key+1);
                    shell_exec($cmd);
                }
                $rename_array = directoryToArray($backup_path, false, true, false);
                krsort($rename_array);
                foreach($rename_array AS $key => $value){
                    $cmd = 'mv '.$value.' '.SITE_ROOT.'./data/backup/backup.'.($key+1);
                    shell_exec($cmd);
                }
                $return_message = lang('core','operation_successful');
            }else{
                $return_message = lang('core','no_linux_permission');
            }
            echo '{
                "statusCode":"200",
                "message":"'.$return_message.'",
                "navTabId":"",
                "rel":"",
                "callbackType":"forward",
                "forwardUrl":"admin.php?mod=backup&action=index",
                "confirmMsg":""
            }';
        }
        break;
    
    case "backup_hand":
        //DEBUG 手动备份
        $is_unix = is_unix();
        $cmd = SITE_ROOT.'./source/source/module/crontab/cron_backup_trigger.sh';
        if($is_unix==1 && function_exists('shell_exec')){
            $cmd = SITE_ROOT.'./source/module/crontab/cron_backup_trigger.sh';
            shell_exec($cmd);
            $return_message = lang('core','operation_successful');
        }else{
            $return_message = lang('core','no_linux_permission');
        }
        echo '{
            "statusCode":"200",
            "message":"'.$return_message.'",
            "navTabId":"",
            "rel":"",
            "callbackType":"forward",
            "forwardUrl":"admin.php?mod=backup&action=index",
            "confirmMsg":""
        }';
        break;

     case "backup_restore":
        $backup_id = isset($_REQUEST['backup_id']) ? $_REQUEST['backup_id'] : '';
        if($backup_id){
            //DEBUG 删除目录
            $is_unix = is_unix();
            if($is_unix==1 && function_exists('shell_exec')){
                $backup_path = SITE_ROOT.'./data/backup/backup.'.$backup_id;
                $tmp_array = directoryToArray($backup_path, false, false, true);
                //DEBUG 导入数据库
				sort($tmp_array);
                $sql_backup_file = $www_backup_file = '';
                $issqlfile = strpos($tmp_array[0],"sql");
                if($issqlfile){
                    $sql_backup_file = $tmp_array[0];
                    $www_backup_file = $tmp_array[1];
                }else{
                    $sql_backup_file = $tmp_array[1];
                    $www_backup_file = $tmp_array[0];
                }
                $sqlfile_path = $sql_backup_file;
                //DEBUG 备份文件存在即执行还原
                if(file_exists($sqlfile_path)){
                    require SITE_ROOT . './config/config_global.php';
                    list($dbhost, $dbport) = explode(':', $dbhost);
                    $query = DB::query("SHOW VARIABLES LIKE 'basedir'");
                    list(, $mysql_base) = DB::fetch($query, MYSQL_NUM);
                    $dbhost = $_G["config"]["db"]['1']["dbhost"];
                    $dbuser = $_G["config"]["db"]['1']["dbuser"];
                    $dbpw = $_G["config"]["db"]['1']["dbpw"];
                    $dbname = $_G["config"]["db"]['1']["dbname"];
                    $dbcharset = $_G["config"]["db"]['1']["dbcharset"];
                    $mysqlbin = $mysql_base == '/' ? '' : addslashes($mysql_base).'/bin/';
                    //DEBUG 前备份
                    //$cmd = SITE_ROOT.'./source/module/crontab/cron_backup_trigger.sh';
                    //shell_exec($cmd);
                    //DEBUG 执行还原
                    $cmd = 'gunzip -c '.$sqlfile_path.' | '.$mysqlbin.'mysql --host="'.$dbhost.($dbport ? (is_numeric($dbport) ? ' --port='.$dbport : ' --socket="'.$dbport.'"') : '').'" --user="'.$dbuser.'" --password="'.$dbpw.'" "'.$dbname.'" '.'';
                    //DEBUG 测试 file_put_contents('test.txt', $cmd);
                    shell_exec($cmd);
                    //DEBUG 解压覆盖源文件
                    $cmd = 'tar -xzvf '.$www_backup_file.' -C '.SITE_ROOT;
                    $return_message = lang('core','operation_successful');
                }else{
                    $return_message = lang('core','operation_failed');
                }
            }else{
                $return_message = lang('core','no_linux_permission');
            }
            echo '{
                "statusCode":"200",
                "message":"'.$return_message.'",
                "navTabId":"",
                "rel":"",
                "callbackType":"forward",
                "forwardUrl":"admin.php?mod=backup&action=index",
                "confirmMsg":""
            }';
        }
        break;
    
    default:
        //DEBUG 查询并返回信息链接
        $page_array = array();
        $backup_dir = SITE_ROOT.'./data/backup';
        $backup_dir_array = directoryToArray($backup_dir, false, true, FALSE);
        foreach($backup_dir_array AS $key => $value){
           $backup_id = substr($value, -1);
           $page_array[$backup_id]['path'] = str_replace(SITE_ROOT, '', $value.DIRECTORY_SEPARATOR);
           $tmp_array = directoryToArray($value, false, false, true);
            if($issqlfile){
                $sql_backup_file = $tmp_array[0];
                $www_backup_file = $tmp_array[1];
            }else{
                $sql_backup_file = $tmp_array[1];
                $www_backup_file = $tmp_array[0];
            }
           $page_array[$backup_id]['files_sql'] = str_replace($value.DIRECTORY_SEPARATOR, '', $sql_backup_file);
           $page_array[$backup_id]['files_www'] = str_replace($value.DIRECTORY_SEPARATOR, '', $www_backup_file);
        }
        ksort($page_array);
        include template('admin/backup/list');
        break;
}
?>