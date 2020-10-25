<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\saf;

use tfc\ap\UserIdentity;
use tfc\ap\Registry;
use tfc\mvc\Mvc;
use tfc\log\Logger;
use tfc\log\LogStream;

/**
 * Log class file
 * 日志处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Log.php 1 2013-04-05 19:53:06Z huan.song $
 * @package tfc.saf
 * @since 1.0
 */
class Log
{
    /**
     * @var instance of tfc\log\Logger
     */
    protected static $_logger = null;

    /**
     * 打印notice日志
     * @param mixed $event
     * @param string $method
     * @return void
     */
    public static function notice($event, $method = '')
    {
        self::write('notice', $event, $method);
    }

    /**
     * 打印warning日志，如果是测试环境，则输出追溯调用的文件和代码行
     * @param mixed $event
     * @param integer $errno
     * @param string $method
     * @return void
     */
    public static function warning($event, $errno = 0, $method = '')
    {
        self::write('warning', $event, $method, $errno);
        if (DEBUG) {
            $wfBackTrace = self::getLogger()->getBackTrace() . "\n<br/>";
            $wfBackTrace .= 'err_no[' . $errno . "]\n<br/>";
            $wfBackTrace .= 'method[' . $method . "]\n<br/>";
            $wfBackTrace .= 'msg[' . (is_array($event) ? serialize($event) : $event) . "]\n<br/>";
            Registry::set('warning_backtrace', $wfBackTrace);
        }
    }

    /**
     * 打印debug日志
     * @param mixed $event
     * @param string $method
     * @return void
     */
    public static function debug($event, $method = '')
    {
        static $priority = 'debug';

        if (DEBUG) {
            if (!self::getLogger()->hasWriter($priority)) {
                $writer = self::getLogger()->getWriter('notice');
                self::getLogger()->addWriter($writer, $priority);
            }

            self::write($priority, $event, $method);
        }
    }

    /**
     * 打印info日志
     * @param mixed $event
     * @param string $method
     * @return void
     */
    public static function info($event, $method = '')
    {
        static $priority = 'info';

        if (!self::getLogger()->hasWriter($priority)) {
            $writer = self::getLogger()->getWriter('notice');
            self::getLogger()->addWriter($writer, $priority);
        }

        self::write($priority, $event, $method);
    }

    /**
     * 打印日志
     * @param string $priority
     * @param string $event
     * @param string $method
     * @param integer $errno
     * @return void
     */
    public static function write($priority, $event, $method = '', $errno = 0)
    {
        $common = array(
            'app' => APP_NAME,
            'err_no' => $errno,
            'user_id' => UserIdentity::getId(),
            'module' => Mvc::$module,
            'controller' => Mvc::$controller,
            'action' => Mvc::$action
        );

        if (!is_array($event)) {
            $event = array('msg' => $event);
        }

        $events = array_merge($common, $event);
        self::getLogger()->write($events, $priority, $method);
    }

    /**
     * 获取日志处理类
     * @return \tfc\log\Logger
     */
    public static function getLogger()
    {
        if (self::$_logger === null) {
            self::setLogger();
        }

        return self::$_logger;
    }

    /**
     * 设置日志处理类
     * @param \tfc\log\Logger
     * @return void
     */
    public static function setLogger(Logger $logger = null)
    {
        if ($logger === null) {
            $logger = new Logger();
            $file = DIR_LOG_APP . DS . APP_NAME . '.log.' . date('YmdH');
            $wfFile = DIR_LOG_APP . DS . APP_NAME . '.log.wf.' . date('Ymd');

            $writer = new LogStream($file);
            $logger->addWriter($writer, 'notice');

            $writer = new LogStream($wfFile);
            $logger->addWriter($writer, 'warning');
        }

        self::$_logger = $logger;
    }

    /**
     * 获取日志ID
     * @return integer
     */
    public static function getId()
    {
        return self::getLogger()->getId();
    }

    /**
     * 打印进度说明
     * @param string $traceMsg
     * @return void
     */
    public static function echoTrace($traceMsg)
    {
        echo '<strong style="color: blue">', $traceMsg, '</strong><br/>';
    }

    /**
     * 打印错误并退出
     * @param integer $line
     * @param string $errMsg
     * @return void
     */
    public static function errExit($line, $errMsg)
    {
        echo '<strong style="color: red">Line: ', $line, '. Msg: ', $errMsg, '</strong>';
        exit;
    }
}
