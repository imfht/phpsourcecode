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
 * Log abstract class file
 * 日志处理基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Log.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.log
 * @since 1.0
 */
abstract class Log
{
    /**
     * @var array 日志级别
     */
    protected $_priorities = array(
        0 => 'EMERG',
        1 => 'ALERT',
        2 => 'CRIT',
        3 => 'ERR',
        4 => 'WARNING',
        5 => 'NOTICE',
        6 => 'INFO',
        7 => 'DEBUG'
    );

    /**
     * @var string key value left delimiter
     */
    protected $_leftDelimiter = '[';

    /**
     * @var string key value right delimiter
     */
    protected $_rightDelimiter = ']';

    /**
     * @var string 如果日志值是数组，将数组转换成字符串的函数
     */
    protected $_arrayProcessFunc = 'serialize';

    /**
     * 魔术方法：以日志级别作为方法名时，会自动跳转到此方法
     * @param string $priority
     * @param array $events
     * @return void
     */
    public function __call($priority, $events)
    {
        $this->log($events[0], $priority);
    }

    /**
     * 为日志内容添加“级别”和“时间”字段，通过调用子类打印日志的方法，打印日志
     * @param string|array $events
     * @param string $priority
     * @return void
     * @throws ErrorException 如果日志级别不存在，抛出异常
     */
    public function log($events, $priority)
    {
        $priority = strtoupper($priority);
        if (!$this->hasPriority($priority)) {
            throw new ErrorException(sprintf(
                'Log bad log priority: "%s"', $priority
            ));
        }

        if (is_array($events)) {
            $events = $this->format($events);
        }

        $logs = array(
            'priority' => $priority,
            'dt_create' => date('Y-m-d H:i:s'),
            'event' => $events
        );
        $this->_write($logs);
    }

    /**
     * 格式化日志内容
     * @param array $events
     * @return string
     */
    public function format(array $events)
    {
        $log = '';

        $events = (array) $events;
        foreach ($events as $key => $value) {
            if (is_array($value)) {
                $value = call_user_func($this->_arrayProcessFunc, $value);
            }

            $log .= $key . $this->_leftDelimiter . $value . $this->_rightDelimiter . ' ';
        }
        $log = substr($log, 0, -1);

        return $log;
    }

    /**
     * 获取所有的日志级别
     * @return array
     */
    public function getPriorities()
    {
        return $this->_priorities;
    }

    /**
     * 添加一个日志级别
     * @param string $priority
     * @return \tfc\log\Log
     */
    public function addPriority($priority)
    {
        $priority = strtoupper($priority);
        if (!$this->hasPriority($priority)) {
            $this->_priorities[] = $priority;
        }

        return $this;
    }

    /**
     * 判断日志级别是否已经存在
     * @param string $priority
     * @return boolean
     */
    public function hasPriority($priority)
    {
        return in_array($priority, $this->_priorities);
    }

    /**
     * 获取分隔键值的左分隔符
     * @return string
     */
    public function getLeftDelimiter()
    {
        return $this->_leftDelimiter;
    }

    /**
     * 设置分隔键值的左分隔符
     * @param string $leftDelimiter
     * @return \tfc\log\Log
     */
    public function setLeftDelimiter($leftDelimiter)
    {
        $this->_leftDelimiter = (string) $leftDelimiter;
        return $this;
    }

    /**
     * 获取分隔键值的右分隔符
     * @return string
     */
    public function getRightDelimiter()
    {
        return $this->_rightDelimiter;
    }

    /**
     * 设置分隔键值的右分隔符
     * @param string $rightDelimiter
     * @return \tfc\log\Log
     */
    public function setRightDelimiter($rightDelimiter)
    {
        $this->_rightDelimiter = (string) $rightDelimiter;
        return $this;
    }

    /**
     * 关闭文件指针或数据库的逻辑等，需要通过子类重写
     * @return void
     */
    public function shutdown()
    {
    }

    /**
     * 析构方法：关闭文件指针或数据库等
     * @return void
     */
    public function __destruct()
    {
        $this->shutdown();
    }

    /**
     * 实现打印具体日志的逻辑，需要通过子类重写
     * @param array $logs
     * @return void
     */
    abstract protected function _write(array $logs);
}
