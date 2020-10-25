<?php

define('CONST_APP_WEBHOOK_VERSION', '1.1.0');
define('CONST_APP_ROOT_DIR_NAME', __DIR__);


/**
 * Description of ExceptionWebHookLog
 *
 * @author kami
 */
class ExceptionWebHookLog extends Exception{
    
    /**
     * 创建异常类
     * @param string $message 异常信息
     * @param string $code 异常代码
     * @param string $previous 
     */
    public function __construct($message, $code, $previous=null) {
        parent::__construct($message, $code, $previous);
        CWebhookLog::AppendLog("ExceptionWebHookLog {$code} ".date('Y-m-d H:i:s') . ' version='.CONST_APP_WEBHOOK_VERSION, $message,'ERROR-EXCEPTION.'.date('Ymd'));
    }
}

/**
 * 用来记录日志信息，方便调试，支持日志相关的CLASS
 * @author kami
 */
class CWebhookLog{
    
    /**
     * 生成日志名称
     * @return string
     */
    public static function MakeFullLogName($logname=null){
        if(null === $logname){
            $logname=date('Ymd');
        }
        $logname.='.log';
        return self::GetLogDirectory().DIRECTORY_SEPARATOR.$logname;
    }
    
    /**
     * 获取日志目录
     * @return string
     */
    public static function GetLogDirectory(){
        return CONST_APP_ROOT_DIR_NAME.DIRECTORY_SEPARATOR.'logs';
    }
    
    /**
     * 添加日志
     */
    public static function AppendLog($title,$content,$logname=null){
        $full_logname=self::MakeFullLogName($logname);
        if(!is_dir(dirname($full_logname))){
            mkdir(dirname($full_logname), 0777,true); //目录不存在创建目录
        }
        
        //读取文件
        $fp=fopen($full_logname, 'a+');
        if(!$fp){
            throw new ExceptionWebHookLog('File open failed:'.$full_logname.__METHOD__.__LINE__,'1604181118');
        }
        
        fwrite($fp, $title."\n>>>\n".$content."\n<<<\n"."----------------\n");
        fclose($fp);
    }
    
    /**
     * 获取某个目录的所有文件
     * 
     */
    public static function LogDirectoryTree() {
        $files = array();
        $dirpath = realpath(self::GetLogDirectory());
        $filenames = scandir(self::GetLogDirectory());
 
        foreach ($filenames as $filename) {
            if ($filename=='.' || $filename=='..'){
                continue;
            }
            $file = $dirpath . DIRECTORY_SEPARATOR . $filename;
            if (is_dir($file)){
                $files = array_merge($files, self::treeDirectory($file));
            }else{
                $files[] = $file;
            }
        }
        return $files;
    }
}