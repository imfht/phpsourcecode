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
 * AbstractDb abstract class file
 * 数据库操作基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: AbstractDb.php 1 2013-05-18 14:58:59Z huan.song $
 * @package tdo
 * @since 1.0
 */
abstract class AbstractDb extends Cache
{
    /**
     * @var boolean 是否缓存查询结果
     */
    public $isCached = true;

    /**
     * @var string 数据库配置名
     */
    protected $_clusterName = null;

    /**
     * @var string 表前缀
     */
    protected $_tblPrefix = null;

    /**
     * @var instance of tfc\saf\DbProxy
     */
    protected $_dbProxy = null;

    /**
     * @var instance of tdo\CommandBuilder
     */
    protected $_commandBuilder = null;

    /**
     * 获取多个结果集，不存缓存
     * @param string $sql
     * @param mixed $params
     * @param boolean $foundRows
     * @return array|false
     */
    public function fetchAllNoCache($sql, $params = null, $foundRows = true)
    {
        $result = $this->getDbProxy()->fetchAll($sql, $params);
        if (is_array($result)) {
            if ($foundRows) {
                $total = $this->getFoundRows();
                $result = array(
                    'rows' => $result,
                    'total' => $total
                );
            }

            return $result;
        }

        return false;
    }

    /**
     * 获取多个结果集
     * @param string $sql
     * @param mixed $params
     * @param integer $fetchMode
     * @return array|false
     */
    public function fetchAll($sql, $params = null, $fetchMode = \PDO::FETCH_ASSOC)
    {
        $key = '';
        if ($this->isCached) {
            $key = sprintf('FETCH_ALL [SQL: %s] [PARAMS: %s] [MODE: %d]', $sql, json_encode((array) $params), $fetchMode);
            if ($this->has($key)) {
                return $this->get($key);
            }
        }

        $result = $this->getDbProxy()->fetchAll($sql, $params, $fetchMode);
        if (is_array($result)) {
            if ($this->isCached) {
                $this->set($key, $result);
            }

            return $result;
        }

        return false;
    }

    /**
     * 获取一条结果集
     * @param string $sql
     * @param mixed $params
     * @param integer $fetchMode
     * @param integer $cursor
     * @param integer $offset
     * @return mixed
     */
    public function fetch($sql, $params = null, $fetchMode = \PDO::FETCH_ASSOC, $cursor = null, $offset = null)
    {
        $key = '';
        if ($this->isCached) {
            $key = sprintf('FETCH [SQL: %s] [PARAMS: %s] [MODE: %d] [CURSOR: %d] [OFFSET: %d]', $sql, json_encode((array) $params), $fetchMode, $cursor, $offset);
            if ($this->has($key)) {
                return $this->get($key);
            }
        }

        $result = $this->getDbProxy()->fetch($sql, $params, $fetchMode, $cursor, $offset);
        if ($result !== false) {
            if ($this->isCached) {
                $this->set($key, $result);
            }

            return $result;
        }

        return false;
    }

    /**
     * 获取多个结果集中指定列的结果
     * @param string $sql
     * @param mixed $params
     * @param integer $columnNumber
     * @return array|false
     */
    public function fetchScalar($sql, $params = null, $columnNumber = 0)
    {
        $key = '';
        if ($this->isCached) {
            $key = sprintf('FETCH_SCALAR [SQL: %s] [PARAMS: %s] [COLUMN: %d]', $sql, json_encode((array) $params), $columnNumber);
            if ($this->has($key)) {
                return $this->get($key);
            }
        }

        $result = $this->getDbProxy()->fetchScalar($sql, $params, $columnNumber);
        if (is_array($result)) {
            if ($this->isCached) {
                $this->set($key, $result);
            }

            return $result;
        }

        return false;
    }

    /**
     * 获取一条结果集，并且以字段名为键返回
     * @param string $sql
     * @param mixed $params
     * @return array|false
     */
    public function fetchAssoc($sql, $params = null)
    {
        $result = $this->fetch($sql, $params);
        if (is_array($result)) {
            return $result;
        }

        return false;
    }

    /**
     * 获取一个结果集中指定列的结果
     * @param string $sql
     * @param mixed $params
     * @param integer $columnNumber
     * @return mixed
     */
    public function fetchColumn($sql, $params = null, $columnNumber = 0)
    {
        $key = '';
        if ($this->isCached) {
            $key = sprintf('FETCH_COLUMN [SQL: %s] [PARAMS: %s] [COLUMN: %d]', $sql, json_encode((array) $params), $columnNumber);
            if ($this->has($key)) {
                return $this->get($key);
            }
        }

        $result = $this->getDbProxy()->fetchColumn($sql, $params, $columnNumber);
        if ($result !== false) {
            if ($this->isCached) {
                $this->set($key, $result);
            }

            return $result;
        }

        return false;
    }

    /**
     * 获取多个结果集，并且以键值对方式返回
     * @param string $sql
     * @param mixed $params
     * @return array|false
     */
    public function fetchPairs($sql, $params = null)
    {
        $key = '';
        if ($this->isCached) {
            $key = sprintf('FETCH_PAIRS [SQL: %s] [PARAMS: %s]', $sql, json_encode((array) $params));
            if ($this->has($key)) {
                return $this->get($key);
            }
        }

        $data = $this->fetchAll($sql, $params, \PDO::FETCH_NUM);
        if (is_array($data)) {
            $result = array();
            foreach ($data as $rows) {
                if (isset($rows[0]) && isset($rows[1])) {
                    $result[$rows[0]] = $rows[1];
                }
            }

            if ($this->isCached) {
                $this->set($key, $result);
            }

            return $result;
        }

        return false;
    }

    /**
     * 新增数据
     * @param string $sql
     * @param mixed $params
     * @return integer|false
     */
    public function insert($sql, $params = null)
    {
        $result = $this->query($sql, $params);
        if ($result) {
            $lastInsertId = $this->getDbProxy()->getLastInsertId();
            if ($this->isCached && $lastInsertId > 0) {
                $this->flush();
            }

            return $lastInsertId;
        }

        return false;
    }

    /**
     * 编辑数据
     * @param string $sql
     * @param mixed $params
     * @return integer|false
     */
    public function update($sql, $params = null)
    {
        $result = $this->query($sql, $params);
        if ($result) {
            $rowCount = $this->getDbProxy()->getRowCount();
            if ($this->isCached && $rowCount > 0) {
                $this->flush();
            }

            return $rowCount;
        }

        return false;
    }

    /**
     * 删除数据
     * @param string $sql
     * @param mixed $params
     * @return integer|false
     */
    public function delete($sql, $params = null)
    {
        $result = $this->query($sql, $params);
        if ($result) {
            $rowCount = $this->getDbProxy()->getRowCount();
            if ($this->isCached && $rowCount > 0) {
                $this->flush();
            }

            return $rowCount;
        }

        return false;
    }

    /**
     * 执行数据库操作
     * @param string $sql
     * @param mixed $params
     * @return boolean
     */
    public function query($sql, $params = null)
    {
        return $this->getDbProxy()->query($sql, $params);
    }

    /**
     * 执行数据库事务操作
     * @param array $commands
     * @return boolean
     */
    public function doTransaction(array $commands = array())
    {
        return $this->getDbProxy()->doTransaction($commands);
    }

    /**
     * 获取最后一次插入记录的ID
     * @return integer
     */
    public function getLastInsertId()
    {
        return $this->getDbProxy()->getLastInsertId();
    }

    /**
     * 获取SQL语句执行后影响的行数
     * @return integer
     */
    public function getRowCount()
    {
        return $this->getDbProxy()->getRowCount();
    }

    /**
     * 获取"SELECT SQL_CALC_FOUND_ROWS"语句的查询总数
     * @return integer
     */
    public function getFoundRows()
    {
        $sql = 'SELECT FOUND_ROWS()';
        return $this->getDbProxy()->fetchColumn($sql);
    }

    /**
     * 获取表前缀
     * @return string
     */
    public function getTblprefix()
    {
        if ($this->_tblPrefix === null) {
            $this->_tblPrefix = $this->getDbProxy()->getTblprefix();
        }

        return $this->_tblPrefix;
    }

    /**
     * 获取数据库代理操作类
     * @return \tfc\saf\DbProxy
     */
    public function getDbProxy()
    {
        if ($this->_dbProxy === null) {
            $clusterName = $this->getClusterName();
            $className = 'tfc\\saf\\DbProxy::' . $clusterName;
            if (($dbProxy = Singleton::get($className)) === null) {
                $dbProxy = new DbProxy($clusterName);
                Singleton::set($className, $dbProxy);
            }

            $this->_dbProxy = $dbProxy;
        }

        return $this->_dbProxy;
    }

    /**
     * 获取创建简单的执行命令类
     * @return \tdo\CommandBuilder
     */
    public function getCommandBuilder()
    {
        if ($this->_commandBuilder === null) {
            $this->_commandBuilder = Singleton::getInstance('tdo\\CommandBuilder');
        }

        return $this->_commandBuilder;
    }

    /**
     * 获取数据库配置名
     * @return string
     */
    public function getClusterName()
    {
        return $this->_clusterName;
    }
}
