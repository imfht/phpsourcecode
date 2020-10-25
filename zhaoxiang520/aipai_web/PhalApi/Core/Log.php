<?php
/**
 * 一个统一的Log处理类
 * 如果你有特殊的需求，比如想要生成一个特殊的文件日志，它的路径很特别
 * 如果修改配置文件会影响所有的日志，所以不建议使用这个类来处理，
 * 你可以直接实例化\Log\File来完成你的特殊需求！
 * @since   2016-08-30
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace PhalApi\Core;


use PhalApi\Core\Exception\PAException;
use PhalApi\Core\Log\File;

class Log {

    const DRIVE_FILE = 'file';

    private $handle;

    /**
     * 构造函数，主要是驱动选择
     * @param string $drive
     * @throws PAException
     */
    public function __construct( $drive = '' ) {
        $drive = strtolower($drive?$drive:Config::get('LOG_TYPE'));
        switch ( $drive ){
            case strtolower(self::DRIVE_FILE):
                $this->handle = new File();
                break;
            default:
                throw new PAException('\\PhalApi\\Core\\Log\\'.$drive.T('L_CLASS.L_NOT_EXIST'));
                break;
        }
    }

    /**
     * 记录SQL语句的Log
     * 后期会加入返回的字符中
     * @param $SQL
     * @param $time
     */
    public function recordSQL( $SQL, $time ){
        $message = '[ SQL ] ' . date('Y-m-d H:i:s') . PHP_EOL .
            ' SQL:' . $SQL . PHP_EOL .
            ' Runtime:' . $time . PHP_EOL;
        $this->handle->recordSQL( $message );
    }

    /**
     * 异常记录Log
     * @param \Exception $e
     */
    public function recordException( \Exception $e ){
        $message = '[ Exception ] ' . date('Y-m-d H:i:s') . PHP_EOL .
            ' File:'. $e->getFile() . PHP_EOL . ' Message:' . $e->getMessage() . PHP_EOL .
            ' Trace:'.$e->getTraceAsString().PHP_EOL;
        $this->handle->recordException( $message );
    }


    /**
     * 系统日志记录
     * 主要作用在于性能分析和优化
     */
    public function recordSystem(){
        if (isset($_SERVER['HTTP_HOST'])) {
            $currentUri = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        } else {
            $currentUri = "cmd:" . implode(' ', $_SERVER['argv']);
        }
        $runtime   = number_format(microtime(true) - PHALAPI_START_TIME, 6);
        $memoryUse = number_format((memory_get_usage() - PHALAPI_START_MEM) / 1024, 2);
        $timeStr   = ' 运行时间：' . $runtime . 's'. PHP_EOL;
        $reqStr    = ' 吞吐率：'   . number_format(1 / $runtime, 2) . 'req/s'. PHP_EOL;
        $memoryStr = ' 内存消耗：' . $memoryUse . 'kb'. PHP_EOL;
        $fileLoad  = ' 文件加载：' . count(get_included_files()) . PHP_EOL;

        $message = '[ System ] '. date('Y-m-d H:i:s') . PHP_EOL
            . $currentUri . $timeStr . $reqStr . $memoryStr . $fileLoad . PHP_EOL;
        $this->handle->recordSystem( $message );
    }

    /**
     * 记录所有Api输入输出以及操作人员信息
     * @param $response
     */
    public function recordApi( $response ){
        $message = '[ Api ] '. date('Y-m-d H:i:s') . PHP_EOL .
            ' Uri: ' . URL::$module . '/' . URL::$class . '/' . URL::$action . PHP_EOL .
            ' UserId: ' . HTTP::$uid . PHP_EOL .
            ' UserName: ' . HTTP::$userName . PHP_EOL .
            ' UserAccount: ' . HTTP::$userAccount . PHP_EOL .
            ' Request: ' . json_encode(HTTP::getAll()) . PHP_EOL .
            ' Response: ' . json_encode($response) . PHP_EOL;
        $this->handle->recordSystem( $message );
    }
}