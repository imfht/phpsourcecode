<?php
/**
 * Log.class.php
 * @author: 七觞酒
 * @email： 739800600@qq.com
 * @date: 14-9-6
*/

namespace framework\libraries;
use framework\Templi;

/**
 * 日志类
 * Class Log
 */
class Log
{
    /** 日志文件权限 */
    const FILE_WRITE_MODE = 0777;
    private $_enabled = true;
    /** @var  string 日志文件目录 */
    private $_logPath;
    /** @var array 错误类型 */
    protected $_levels	= array('ERROR' => '1', 'DEBUG' => '2',  'INFO' => '3', 'ALL' => '4');

    /**
     * 构造方法
     */
    public function __construct()
    {
        $this->_logPath = Templi::getApp()->getConfig('log_path');
        if (empty($this->_logFile)) {
            $this->_logFile = '/tmp/';
        }
        if(!is_dir($this->_logPath) || !is_writable($this->_logPath)){
            $this->_enabled = false;
        }
    }

    /**
     * 写日志
     * @param string $level
     * @param string $msg
     * @param string $prefix
     * @return bool
     */
    public function writeLog($msg, $level='ERROR', $prefix='log')
    {
        if($this->_enabled==false){
            return false;
        }
        $logFile = $this->_logPath.$prefix.date('Y-m-d').'.log';
        if(file_exists($logFile) && !is_writable($logFile)){
            @chmod($logFile, self::FILE_WRITE_MODE);
        }
        $level = strtoupper($level);
        if (!isset($this->_levels[$level])){
            return false;
        }
        $message = $level.'-'.date('Y-m-d H:i:s').'-->'.$msg."\n";
        file_put_contents($logFile, $message);
        return true;
    }
} 