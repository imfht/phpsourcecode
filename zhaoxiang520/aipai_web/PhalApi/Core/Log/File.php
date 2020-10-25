<?php
/**
 * Log.php
 * @since   2016-08-30
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace PhalApi\Core\Log;


use PhalApi\Core\Config;
use PhalApi\Core\Exception\PAException;

class File {

    private $logPath;
    public function __construct( $path = '' ) {
        $path = ucfirst($path?$path:Config::get('LOG_PATH'));
        if( !is_writable(DOCUMENT_ROOT.$path) ){
            throw new PAException(T('L_DIR').'('.DOCUMENT_ROOT.$path.')'.T('L_AUTH.L_ERROR'));
        }
        $this->logPath = DOCUMENT_ROOT .DS. trim($path, DS);
        if( !file_exists($this->logPath) ){
            mkdir($this->logPath, 0755, true);
        }
    }

    /**
     * 记录数据库操作语句日志
     * 一个月一个文件夹，一天一个文件的方式记录
     * @param $message
     * @throws PAException
     */
    public function recordSQL( $message ){
        $realPath = $this->logPath.DS.'SQL'.DS.date('Y-m');
        if( !file_exists($realPath) ){
            mkdir($realPath, 0755, true);
        }
        $fileName = $realPath.DS.date('Y-m-d').'.log';
        $res = file_put_contents($fileName, $message, FILE_APPEND);
        if( $res === FALSE ){
            throw new PAException(T('L_UNKNOWN'));
        }
    }

    /**
     * 记录异常类日志
     * 一年一个文件夹，一个月一个文件
     * @param $message
     * @throws \Exception
     */
    public function recordException( $message ){
        $realPath = $this->logPath.DS.'Exception'.DS.date('Y');
        if( !file_exists($realPath) ){
            mkdir($realPath, 0755, true);
        }
        $fileName = $realPath.DS.date('Y-m').'.log';
        $res = file_put_contents($fileName, $message, FILE_APPEND);
        if( $res === FALSE ){
            throw new PAException(T('L_UNKNOWN'));
        }
    }

    /**
     * 系统日志记录
     * 一个月一个文件夹，一天一个文件的方式记录
     * @param $message
     * @throws PAException
     */
    public function recordSystem( $message ){
        $realPath = $this->logPath.DS.'System'.DS.date('Y-m');
        if( !file_exists($realPath) ){
            mkdir($realPath, 0755, true);
        }
        $fileName = $realPath.DS.date('Y-m-d').'.log';
        $res = file_put_contents($fileName, $message, FILE_APPEND);
        if( $res === FALSE ){
            throw new PAException(T('L_UNKNOWN'));
        }
    }

    /**
     * 接口请求的细节日志
     * 一天一个文件夹，每小时一个文件
     * @param $message
     * @throws PAException
     */
    public function recordApi( $message ){
        $realPath = $this->logPath.DS.'Api'.DS.date('Y-m-d');
        if( !file_exists($realPath) ){
            mkdir($realPath, 0755, true);
        }
        $fileName = $realPath.DS.date('Y-m-d-H').'.log';
        $res = file_put_contents($fileName, $message, FILE_APPEND);
        if( $res === FALSE ){
            throw new PAException(T('L_UNKNOWN'));
        }
    }

}