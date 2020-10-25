<?php
/**
 * Trotri Data Objects
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tdo;

use tfc\ap\Cache;
use tfc\ap\Singleton;
use tfc\saf\DbProxy;

/**
 * Profile class file
 * Profile层类，记录扩展表的结果集
 * <pre>
 * 主表的扩展表：需要将profile_id和profile_key建联合唯一
 * CREATE TABLE `table_profile` (
 *   `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
 *   `profile_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '主表的主键',
 *   `profile_key` varchar(255) NOT NULL DEFAULT '' COMMENT '扩展Key',
 *   `profile_value` longtext COMMENT '扩展Value',
 *   PRIMARY KEY (`id`),
 *   UNIQUE KEY `uk_id_key` (`profile_id`, `profile_key`)
 * );
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Profile.php 1 2013-05-18 14:58:59Z huan.song $
 * @package tdo
 * @since 1.0
 */
class Profile extends Cache
{
    /**
     * @var boolean 是否缓存查询结果
     */
    public $isCached = true;

    /**
     * @var instance of tfc\saf\DbProxy
     */
    protected $_dbProxy = null;

    /**
     * @var string 表名
     */
    protected $_tableName = null;

    /**
     * @var array 所有的列名
     */
    protected $_columnNames = array('profile_id', 'profile_key', 'profile_value');

    /**
     * @var integer 寄存profile_id值
     */
    protected $_idValue;

    /**
     * @var array instances of tdo\Profile
     */
    protected static $_instances = array();

    /**
     * 构造方法：初始化表名、ProfileID值和数据库操作类
     * @param string $tableName
     * @param integer $idValue
     * @param \tfc\saf\DbProxy $dbProxy
     */
    protected function __construct($tableName, $idValue, DbProxy $dbProxy)
    {
        $this->_tableName = strtolower($tableName);
        $this->_idValue = (int) $idValue;
        $this->_dbProxy = $dbProxy;
    }

    /**
     * 单例模式：获取本类的实例
     * @param string $tableName
     * @param integer $idValue
     * @param \tfc\saf\DbProxy $dbProxy
     * @return \tdo\Profile
     */
    public static function getInstance($tableName, $idValue, DbProxy $dbProxy)
    {
        $tableName = strtolower($tableName);
        $idValue = (int) $idValue;
        $name = $tableName . '[' . $idValue . ']';
        if (!isset(self::$_instances[$name])) {
            self::$_instances[$name] = new self($tableName, $idValue, $dbProxy);
        }

        return self::$_instances[$name];
    }

    /**
     * 通过profile_id，获取所有的记录
     * @return array
     */
    public function findAll()
    {
        $sql = $this->getCommandBuilder()->createFind($this->getTableName(), $this->_columnNames, $this->getIDCondition());
        if ($this->isCached) {
            if ($this->has($sql)) {
                return $this->get($sql);
            }
        }

        $data = array();
        $rows = $this->getDbProxy()->fetchAll($sql);
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $data[$row['profile_key']] = $row['profile_value'];
            }
        }

        if ($this->isCached) {
            $this->set($sql, $data);
        }

        return $data;
    }

    /**
     * 通过profile_id，删除所有的记录
     * @return integer|false
     */
    public function deleteAll()
    {
        $sql = $this->getCommandBuilder()->createDelete($this->getTableName(), $this->getIDCondition());
        if ($this->getDbProxy()->query($sql)) {
            if ($this->isCached) { $this->flush(); }
            return $this->getDbProxy()->getRowCount();
        }

        return false;
    }

    /**
     * 通过profile_id和profile_key，查询一条记录
     * @param string $key
     * @return mixed 
     */
    public function find($key)
    {
        $sql = $this->getCommandBuilder()->createFind($this->getTableName(), array('profile_value'), $this->getUKCondition());
        $ckey = sprintf('FIND [SQL: %s] [KEY: %s]', $sql, $key);

        if ($this->isCached) {
            if ($this->has($ckey)) {
                return $this->get($ckey);
            }
        }

        $value = $this->getDbProxy()->fetchColumn($sql, $key);
        if ($this->isCached) {
            $this->set($ckey, $value);
        }

        return $value;
    }

    /**
     * 批量新增和编辑记录
     * @param array $attributes
     * @return boolean
     */
    public function save($attributes)
    {
        $result = true;
        $rows = $this->findAll();
        foreach ($attributes as $key => $value) {
            $func = 'insert';
            if (isset($rows[$key])) {
                $func = 'update';
                if ($rows[$key] === $value) {
                    continue;
                }
            }

            if (!$this->$func($key, $value)) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * 新增一条记录
     * @param string $key
     * @param mixed $value
     * @return integer|false
     */
    public function insert($key, $value)
    {
        $sql = $this->getCommandBuilder()->createInsert($this->getTableName(), $this->_columnNames);
        $attributes = array(
            'profile_id' => $this->_idValue,
            'profile_key' => $key,
            'profile_value' => $value
        );

        if ($this->getDbProxy()->query($sql, $attributes)) {
            if ($this->isCached) { $this->flush(); }
            return $this->getDbProxy()->getLastInsertId();
        }

        return false;
    }

    /**
     * 编辑一条记录
     * @param string $key
     * @param mixed $value
     * @return integer|false
     */
    public function update($key, $value)
    {
        $sql = $this->getCommandBuilder()->createUpdate($this->getTableName(), array('profile_value'), $this->getUKCondition());
        $attributes = array(
            'profile_value' => $value,
            'profile_key' => $key
        );

        if ($this->getDbProxy()->query($sql, $attributes)) {
            if ($this->isCached) { $this->flush(); }
            return $this->getDbProxy()->getRowCount();
        }

        return false;
    }

    /**
     * 删除一条记录
     * @param string $key
     * @return integer|false
     */
    public function delete($key)
    {
        $sql = $this->getCommandBuilder()->createDelete($this->getTableName(), $this->getUKCondition());
        if ($this->getDbProxy()->query($sql, $key)) {
            if ($this->isCached) { $this->flush(); }
            return $this->getDbProxy()->getRowCount();
        }

        return false;
    }

    /**
     * 通过profile_id名，获取Where条件
     * @return string
     */
    public function getIDCondition()
    {
        static $condition = null;
        if ($condition === null) {
            $condition = $this->getCommandBuilder()->quoteColumnName('profile_id') . ' = \'' . $this->_idValue . '\'';
        }

        return $condition;
    }

    /**
     * 通过profile_id和profile_key名，获取Where条件
     * @return string
     */
    public function getUKCondition()
    {
        static $condition = null;
        if ($condition === null) {
            $condition = $this->getIDCondition() . ' AND ' . $this->getCommandBuilder()->quoteColumnName('profile_key') . ' = ' . CommandBuilder::PLACE_HOLDERS;
        }

        return $condition;
    }

    /**
     * 获取表名
     * @return string
     */
    public function getTableName()
    {
        return $this->_tableName;
    }

    /**
     * 获取数据库操作类
     * @return \tfc\saf\DbProxy
     */
    public function getDbProxy()
    {
        return $this->_dbProxy;
    }

    /**
     * 获取创建简单的DB执行命令类
     * @return \tdo\CommandBuilder
     */
    public function getCommandBuilder()
    {
        return Singleton::getInstance('tdo\\CommandBuilder');
    }
}
