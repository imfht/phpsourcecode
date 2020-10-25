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

use tfc\db\Driver;

/**
 * LogDb class file
 * 数据库方式处理日志
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: LogDb.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.log
 * @since 1.0
 */
class LogDb extends Log
{
    /**
     * @var instance of tfc\db\Driver
     */
    protected $_driver = null;

    /**
     * @var string 日志表名
     */
    protected $_table = 'log';

    /**
     * @var boolean 是否自动创建Log表
     */
    protected $_autoCreateLogTable = true;

    /**
     * 构造方法：初始化数据库链接类、Log表名、是否自动创建Log表
     * @param \tfc\db\Driver $driver
     * @param string $table
     * @param boolean $autoCreateLogTable
     */
    public function __construct(Driver $driver, $table = null, $autoCreateLogTable = false)
    {
        $this->_driver = $driver;
        if ($table !== null) {
            $this->_table = (string) $table;
        }
        $this->_autoCreateLogTable = (boolean) $autoCreateLogTable;
        if ($this->_autoCreateLogTable) {
            $this->createLogTable($this->_table);
        }
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\log\Log::shutdown()
     */
    public function shutdown()
    {
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\log\Log::_write()
     */
    protected function _write(array $logs)
    {
        $event = $this->_driver->quoteValue($logs['event']);
        $sql = "INSERT INTO {$this->_table} (event, priority, dt_create) VALUES ($event, '{$logs['priority']}', '{$logs['dt_create']}')";
        return $this->_driver->getPdo()->query($sql);
    }

    /**
     * 通过表名，创建日志表
     * @param string $table
     * @return boolean
     */
    public function createLogTable($table)
    {
        $sql = "
CREATE TABLE IF NOT EXISTS $table (
    id           BIGINT(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    event        TEXT       NOT NULL COMMENT 'LOG的内容',
    priority     ENUM('DB_EMERG','DB_ALERT','DB_CRIT','DB_ERR','DB_WARNING','DB_NOTICE','DB_INFO','DB_DEBUG') NOT NULL COMMENT 'LOG类型名称',
    dt_created   DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT '创建LOG的时间',
    PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='日志表';
";
        return $this->_driver->getPdo()->query($sql);
    }
}
