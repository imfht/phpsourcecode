<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\log;

use tfc\ap\ErrorException;

/**
 * Logger class file
 * 日志处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Logger.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.log
 * @since 1.0
 */
class Logger
{
    /**
     * @var string 日志级别
     */
    const LEVEL_EMERG = 'EMERG';
    const LEVEL_ALERT = 'ALERT';
    const LEVEL_CRIT = 'CRIT';
    const LEVEL_ERR = 'ERR';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_NOTICE = 'NOTICE';
    const LEVEL_INFO = 'INFO';
    const LEVEL_DEBUG = 'DEBUG';

    /**
     * @var array instances of tfc\log\Log
     */
    protected $_writers = array();

    /**
     * @var integer 错误日志追溯的函数调用层数
     */
    protected $_traceLevel = 4;

    /**
     * @var integer|null 寄存日志ID
     */
    protected $_logId = null;

    /**
     * 根据日志级别和日志内容，打印日志
     * @param array $events
     * @param string $priority
     * @param string $method
     * @return void
     */
    public function write(array $events, $priority, $method = '')
    {
        $writer = $this->getWriter($priority);
        $commons = array(
            'log_id' => $this->getId(),
            'timestamp' => time(),
            'method' => $method
        );
        $events = array_merge($commons, $events);
        $writer->$priority($events);
    }

    /**
     * 析构方法：关闭所有的文件指针或数据库等
     * @return void
     */
    public function __destruct()
    {
        foreach ($this->getWriters() as $writer) {
            try {
                $writer->shutdown();
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * 获取所有的日志操作类
     * @return \tfc\log\Log
     */
    public function getWriters()
    {
        return $this->_writers;
    }

    /**
     * 设置所有的日志操作类
     * @param array $writers
     * @return \tfc\log\Logger
     * @throws ErrorException 如果设置的日志操作类不正确，抛出异常
     * @throws ErrorException 如果设置的日志操作类不是tfc\log\Log的子类，抛出异常
     */
    public function setWriters(array $writers)
    {
        foreach ($writers as $writer) {
            if (!is_object($writer)) {
                throw new ErrorException(
                    'Logger writer "%s" must be a instance of tfc\log\Log', $writer
                );
            }

            if (!$writer instanceof Log) {
                throw new ErrorException(
                    'Logger writer "%s" must be a instance of tfc\log\Log', get_class($writer)
                );
            }
        }

        $this->_writers = $writers;
        return $this;
    }

    /**
     * 通过级别获取日志操作类
     * @param string $priority
     * @return \tfc\log\Log
     * @throws ErrorException 如果该级别的日志操作类不存在，抛出异常
     */
    public function getWriter($priority)
    {
        if (!$this->hasWriter($priority)) {
            throw new ErrorException(
                'Logger is unable to find the priority "%s".', $priority
            );
        }

        return $this->_writers[$priority];
    }

    /**
     * 通过级别添加日志操作类
     * @param Log $writer
     * @param string $priority
     * @return \tfc\log\Logger
     */
    public function addWriter(Log $writer, $priority)
    {
        $this->_writers[$priority] = $writer;
        return $this;
    }

    /**
     * 判断某个级别的日志操作类是否存在
     * @param string $priority
     * @return boolean
     */
    public function hasWriter($priority)
    {
        return isset($this->_writers[$priority]);
    }

    /**
     * 追溯调用的文件和代码行，并返回
     * @return string
     */
    public function getBackTrace()
    {
        $message = '';
        $traces = debug_backtrace();
        $level = $this->getTraceLevel();
        $count = 0;
        foreach ($traces as $trace) {
            if (!isset($trace['file'], $trace['line'])) {
                continue;
            }

            $message .= "\n<br/>in " . $trace['file'] . ' (' . $trace['line'] . ')';
            if (++$count >= $level) {
                break;
            }
        }

        return $message;
    }

    /**
     * 获取追溯调用的文件级数
     * @return integer
     */
    public function getTraceLevel()
    {
        return $this->_traceLevel;
    }

    /**
     * 设置追溯调用的文件级数
     * @param integer $traceLevel
     * @return \tfc\log\Logger
     * @throws ErrorException 如果追溯调用的级数小于0，抛出异常
     */
    public function setTraceLevel($traceLevel)
    {
        if (($traceLevel = (int) $traceLevel) < 0) {
            throw new ErrorException(sprintf(
                'Logger Trace Level "%d" must be greater and equal than 0.', $traceLevel
            ));
        }

        $this->_traceLevel = $traceLevel;
        return $this;
    }

    /**
     * 获取日志ID
     * @return integer
     */
    public function getId()
    {
        if ($this->_logId === null) {
            $this->setId();
        }

        return $this->_logId;
    }

    /**
     * 设置日志ID
     * @param integer $id
     * @return \tfc\log\Logger
     */
    public function setId($id = null)
    {
        if ($id !== null) {
            $this->_logId = (int) $id;
            return $this;
        }

        $times = gettimeofday();
        $this->_logId = ((int) ($times['sec'] * 100000 + $times['usec'] / 10)) & 0x7FFFFFFF;
        return $this;
    }
}
