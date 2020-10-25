<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\util;

use tfc\ap\interfaces\SessionSaveHandler;
use tfc\db\Driver;

/**
 * DbSession class file
 * 用MySQL数据库存储会话，默认自动创建SESSION表
 * 手动建表时，session_id一定要加主键约束，不然SQL:REPLACE语句会添加很多新记录
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DbSession.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.util
 * @since 1.0
 */
class DbSession implements SessionSaveHandler
{
    /**
     * @var instance of tfc\db\Driver
     */
    protected $_driver = null;

    /**
     * @var \PDO|null PDO类实例
     */
    protected $_pdo = null;

    /**
     * @var string 会话表名
     */
    protected $_table = 'session';

    /**
     * @var boolean 是否自动创建Session表
     */
    protected $_autoCreateSessTable = true;

    /**
     * 构造方法：初始化数据库操作类、Session表名、是否自动创建Session表
     * @param \tfc\db\Driver $driver
     * @param string $table
     * @param boolean $autoCreateSessTable
     */
    public function __construct(Driver $driver, $table = null, $autoCreateSessTable = false)
    {
        $this->_driver = $driver;
        if ($table !== null) {
            $this->_table = (string) $table;
        }

        $this->_autoCreateSessTable = (boolean) $autoCreateSessTable;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\ap\interfaces\SessionSaveHandler::open()
     */
    public function open($path, $name)
    {
        $this->_pdo = $this->_driver->open()->getPdo();

        if ($this->_autoCreateSessTable) {
            $this->createSessTable($this->_table);
        }

        $this->gc((int) ini_get('session.gc_maxlifetime'));
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\ap\interfaces\SessionSaveHandler::close()
     */
    public function close()
    {
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\ap\interfaces\SessionSaveHandler::read()
     */
    public function read($sessId)
    {
        $sql = 'SELECT `data` FROM `' . $this->_table . '` WHERE `session_id` = ? LIMIT 1';

        $statement = $this->_pdo->prepare($sql);
        $statement->bindParam(1, $sessId);
        $statement->execute();
        return $statement->fetchColumn();
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\ap\interfaces\SessionSaveHandler::write()
     */
    public function write($sessId, $data)
    {
        $sql = 'REPLACE INTO `' . $this->_table . '` (`dt_last_access`, `data`, `session_id`) VALUES (?, ?, ?)';
        $dtLastAccess = time();

        $statement = $this->_pdo->prepare($sql);
        $statement->bindParam(1, $dtLastAccess);
        $statement->bindParam(2, $data);
        $statement->bindParam(3, $sessId);
        return $statement->execute();
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\ap\interfaces\SessionSaveHandler::destroy()
     */
    public function destroy($sessId)
    {
        $sql = 'DELETE FROM `' . $this->_table . '` WHERE `session_id` = ?';

        $statement = $this->_pdo->prepare($sql);
        $statement->bindParam(1, $sessId);
        return $statement->execute();
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\ap\interfaces\SessionSaveHandler::gc()
     */
    public function gc($maxLifeTime)
    {
        $sql = 'DELETE FROM `' . $this->_table . '` WHERE `dt_last_access` < ?';
        $dtLastAccess = time() - $maxLifeTime;

        $statement = $this->_pdo->prepare($sql);
        $statement->bindParam(1, $dtLastAccess);
        return $statement->execute();
    }

    /**
     * 通过表名，创建会话表
     * @param string $table
     * @return boolean
     */
    public function createSessTable($table)
    {
        $charset = $this->_driver->getCharset();

        $sql = "
CREATE TABLE IF NOT EXISTS `$table` (
  `session_id` CHAR(32) NOT NULL COMMENT 'SESSION_ID',
  `dt_last_access` INT(10) NOT NULL DEFAULT '0' COMMENT '最后一次访问SESSION的时间',
  `data` TEXT NOT NULL COMMENT 'SESSION的内容',
  `dt_start` INT(10) NOT NULL DEFAULT '0' COMMENT '第一次访问SESSION的时间',
  PRIMARY KEY (`session_id`),
  KEY (`dt_last_access`)
) ENGINE=MyISAM DEFAULT CHARSET=$charset;
";
        return $this->_pdo->exec($sql);
    }

    /**
     * 析构方法：关闭会话
     */
    public function __destruct()
    {
        session_write_close();
    }
}
