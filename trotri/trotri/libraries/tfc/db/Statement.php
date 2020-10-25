<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\db;

use tfc\ap\Application;
use tfc\ap\ErrorException;

/**
 * Statement class file
 * PDO方式预处理并执行SQL
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Statement.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.db
 * @since 1.0
 */
class Statement extends Application
{
    /**
     * @var \PDOStatement|null PDOStatement类实例
     */
    protected $_PDOStatement = null;

    /**
     * @var integer Specifies the fetchMode of column names retrieved in queries
     * Options
     * PDO::FETCH_ASSOC (default)
     * PDO::FETCH_NUM
     * PDO::FETCH_BOTH
     * PDO::FETCH_LAZY
     * PDO::FETCH_OBJ
     * [PDO::FETCH_COLUMN|PDO::FETCH_GROUP]
     */
    protected $_fetchMode = \PDO::FETCH_ASSOC;

    /**
     * @var string 需要绑定执行的SQL语句
     */
    protected $_sql = '';

    /**
     * @var array 需要绑定到PDOStatement的数组
     */
    protected $_params = array();

    /**
     * @var tfc\db\Driver PDO方式连接数据库对象
     */
    protected $_driver = null;

    /**
     * @var integer 用数字索引向PDOStatement绑定数据
     */
    const POSITIONAL = 1;

    /**
     * @var integer 用:named方式向PDOStatement绑定数据
     */
    const NAMED = 2;

    /**
     * @var integer 向PDOStatement绑定数据的方式
     */
    protected $_supportParam = self::POSITIONAL;

    /**
     * 构造方法：初始化PDO方式连接数据库对象
     * @param \tfc\db\Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->_driver = $driver;
    }

    /**
     * 获取所有的结果集
     * @param string $sql
     * @param mixed $params
     * @param integer $fetchMode
     * @return array|false
     */
    public function fetchAll($sql = null, $params = null, $fetchMode = \PDO::FETCH_ASSOC)
    {
        if (!$this->query($sql, $params)) {
            return false;
        }

        if ($fetchMode === null) {
            $fetchMode = $this->getFetchMode();
        }

        return $this->getPDOStatement()->fetchAll($fetchMode);
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
    public function fetch($sql = null, $params = null, $fetchMode = \PDO::FETCH_ASSOC, $cursor = null, $offset = null)
    {
        if (!$this->query($sql, $params)) {
            return false;
        }

        switch (true) {
            case $fetchMode === null:
                return $this->getPDOStatement()->fetch($this->getStatement()->getFetchMode());
            case $cursor === null:
                return $this->getPDOStatement()->fetch($fetchMode);
            case $offset === null:
                return $this->getPDOStatement()->fetch($fetchMode, $cursor);
            default:
                return $this->getPDOStatement()->fetch($fetchMode, $cursor, $offset);
        }
    }

    /**
     * 获取多个结果集中指定列的结果
     * @param string $sql
     * @param mixed $params
     * @param integer $columnNumber
     * @return array|false
     */
    public function fetchScalar($sql = null, $params = null, $columnNumber = 0)
    {
        if (!$this->query($sql, $params)) {
            return false;
        }

        return $this->getPDOStatement()->fetchAll(\PDO::FETCH_COLUMN, $columnNumber);
    }

    /**
     * 获取一个结果集中指定列的结果
     * @param string $sql
     * @param mixed $params
     * @param integer $columnNumber
     * @return mixed
     */
    public function fetchColumn($sql = null, $params = null, $columnNumber = 0)
    {
        if (!$this->query($sql, $params)) {
            return false;
        }

        return $this->getPDOStatement()->fetchColumn($columnNumber);
    }

    /**
     * 执行数据库操作
     * @param string $sql
     * @param mixed $params
     * @return boolean
     */
    public function query($sql = null, $params = null)
    {
        if ($sql !== null) {
            $this->setSql($sql);
        }

        if ($params !== null) {
            if (is_array($params)) {
                $this->bindValues($params);
            }
            else {
                $this->bindParam(1, $params);
            }
        }

        $this->prepare();
        $this->_bindParams();
        return $this->execute();
    }

    /**
     * 为执行准备一条SQL语句
     * @return \tfc\db\Statement
     * @throws ErrorException 如果预处理失败，抛出异常
     */
    public function prepare()
    {
        if ($this->_PDOStatement === null) {
            try {
                $this->_PDOStatement = $this->getDriver()->getPdo()->prepare($this->getSql());
            }
            catch (\PDOException $e) {
                throw new ErrorException(sprintf(
                    'Statement failed to prepare the SQL statement, %s', $e->getMessage()
                ), (int) $e->getCode());
            }
        }

        return $this;
    }

    /**
     * 将准备执行的SQL、参数、PDOStatement类实例置空
     * @return \tfc\db\Statement
     */
    public function reset()
    {
        $this->_sql = '';
        $this->_params = array();
        $this->_PDOStatement = null;
        return $this;
    }

    /**
     * 绑定多个值到预处理语句中的参数 
     * @param array $values
     * @return \tfc\db\Statement
     */
    public function bindValues(array $values)
    {
        if ($values) {
            if ($this->isPositional()) {
                $param = 0;
                foreach ($values as $value) {
                    $this->bindValue(++$param, $value);
                }
            }
            else {
                foreach ($values as $param => $value) {
                    $this->bindValue($param, $value);
                }
            }
        }

        return $this;
    }

    /**
     * 绑定一个值到预处理语句中的参数 
     * @param mixed $param
     * @param mixed $value
     * @param integer|null $dataType
     * @return \tfc\db\Statement
     */
    public function bindValue($param, $value, $dataType = null)
    {
        return $this->bindParam($param, $value, $dataType);
    }

    /**
     * 绑定一个PHP变量到一个预处理语句中的参数 
     * @param mixed $param
     * @param mixed $value
     * @param integer|null $dataType
     * @param integer|null $length
     * @param mixed $driverOptions
     * @return \tfc\db\Statement
     * @throws ErrorException 如果绑定的键不是整型或字符串类型，抛出异常
     * @throws ErrorException 如果当前不支持绑定的键的类型，抛出异常
     */
    public function bindParam($param, &$value, $dataType = null, $length = null, $driverOptions = null)
    {
        if (!is_int($param) && !is_string($param)) {
            throw new ErrorException(sprintf(
                'Statement invalid bind-param position, param "%s" must be string or integer', $param
            ));
        }

        $position = null;
        if ($this->isPositional() && ($tmp = (int) $param) > 0) {
            $position = $tmp;
        }
        elseif ($this->isNamed() && is_string($param)) {
            $position = ($param[0] != ':') ? (':' . $param) : $param;
        }

        if ($position === null) {
            throw new ErrorException(sprintf(
                'Statement invalid bind-param position, supportParam "%d", param "%s"', $this->getSupportParam(), $param
            ));
        }

        if ($dataType === null) {
            $dataType = $this->getDriver()->getPDOType(gettype($value));
        }

        $this->_params[$position] = array(
            'variable'      => $value,
            'dataType'      => $dataType,
            'length'        => $length,
            'driverOptions' => $driverOptions
        );

        return $this;
    }

    /**
     * 绑定多个PHP变量到一个预处理语句中的参数
     * @return \tfc\db\Statement
     * @throws ErrorException 如果绑定参数失败，抛出异常
     */
    protected function _bindParams()
    {
        $params = $this->getParams();
        try {
            foreach ($params as $position => $values) {
                if ($values['length'] === null) {
                    $this->getPDOStatement()->bindParam($position, $values['variable'], $values['dataType']);
                }
                elseif ($values['driverOptions'] === null) {
                    $this->getPDOStatement()->bindParam($position, $values['variable'], $values['dataType'], $values['length']);
                }
                else {
                    $this->getPDOStatement()->bindParam($position, $values['variable'], $values['dataType'], $values['length'], $values['driverOptions']);
                }
            }
        }
        catch (\PDOException $e) {
            throw new ErrorException(sprintf(
                'Statement PDOStatement bind param failed, %s', $e->getMessage()
            ), (int) $e->getCode());
        }

        return $this;
    }

    /**
     * 执行一条预处理语句
     * @param array $params
     * @return boolean
     * @throws ErrorException 如果执行预处理语句失败，抛出异常
     */
    public function execute(array $params = null)
    {
        try {
            return $this->getPDOStatement()->execute($params);
        }
        catch (\PDOException $e) {
            throw new ErrorException(sprintf(
                'Statement PDOStatement execute failed, %s', $e->getMessage()
            ), (int) $e->getCode());
        }
    }

    /**
     * 获取PDOStatement默认的fetch mode
     * @return integer
     */
    public function getFetchMode()
    {
        return $this->_fetchMode;
    }

    /**
     * 为PDOStatement设置默认的fetch mode
     * @param integer $fetchMode
     * @return \tfc\db\Statement
     * @throws ErrorException 如果参数不是有效的类型，抛出异常
     */
    public function setFetchMode($fetchMode = \PDO::FETCH_ASSOC)
    {
        $fetchMode = (int) $fetchMode;
        switch ($fetchMode) {
            case \PDO::FETCH_NUM:
            case \PDO::FETCH_ASSOC:
            case \PDO::FETCH_BOTH:
            case \PDO::FETCH_OBJ:
                $this->_fetchMode = $fetchMode;
                break;
            case \PDO::FETCH_BOUND:
            default:
                throw new ErrorException(sprintf(
                    'Statement invalid fetch mode "%d", fetch mode must be "\PDO::FETCH_NUM", "\PDO::FETCH_ASSOC", "\PDO::FETCH_BOTH", "\PDO::FETCH_OBJ"', $fetchMode
                ));
        }

        return $this;
    }

    /**
     * 获取SQL语句执行后影响的行数
     * @return integer
     * @throws ErrorException 如果获取行数失败，抛出异常
     */
    public function getRowCount()
    {
        try {
            return $this->getPDOStatement()->rowCount();
        }
        catch (\PDOException $e) {
            throw new ErrorException(sprintf(
                'Statement PDOStatement row count failed, %s', $e->getMessage()
            ), (int) $e->getCode());
        }
    }

    /**
     * 获取结果集中的列数
     * @return integer
     * @throws ErrorException 如果获取列数失败，抛出异常
     */
    public function getColumnCount()
    {
        try {
            return $this->getPDOStatement()->columnCount();
        }
        catch (\PDOException $e) {
            throw new ErrorException(sprintf(
                'Statement PDOStatement column count failed, %s', $e->getMessage()
            ), (int) $e->getCode());
        }
    }

    /**
     * 获取错误码
     * @return integer
     * @throws ErrorException 如果获取错误码失败，抛出异常
     */
    public function getErrorCode()
    {
        try {
            return $this->getPDOStatement()->errorCode();
        }
        catch (\PDOException $e) {
            throw new ErrorException(sprintf(
                'Statement PDOStatement error code failed, %s', $e->getMessage()
            ), (int) $e->getCode());
        }
    }

    /**
     * 获取错误信息
     * @return array
     * @throws ErrorException 如果获取错误信息失败，抛出异常
     */
    public function getErrorInfo()
    {
        try {
            return $this->getPDOStatement()->errorInfo();
        }
        catch (\PDOException $e) {
            throw new ErrorException(sprintf(
                'Statement PDOStatement error info failed, %s', $e->getMessage()
            ), (int) $e->getCode());
        }
    }

    /**
     * 获取需要绑定执行的SQL语句
     * @return string
     */
    public function getSql()
    {
        return $this->_sql;
    }

    /**
     * 设置需要绑定执行的SQL语句
     * @param string $sql
     * @return \tfc\db\Statement
     * @throws ErrorException 如果SQL不是字符串类型，抛出异常
     */
    public function setSql($sql)
    {
        if (is_string($sql)) {
            $this->reset();
            $this->_sql = $sql;
            return $this;
        }
        throw new ErrorException(sprintf(
            'Statement set sql "%s" failed, sql must be string', $sql
        ));
    }

    /**
     * 获取绑定到PDOStatement的数组
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * 判断是否使用数字索引向PDOStatement绑定数据
     * @return boolean
     */
    public function isPositional()
    {
        return $this->getSupportParam() === self::POSITIONAL;
    }

    /**
     * 判断是否使用:named方式向PDOStatement绑定数据
     * @return boolean
     */
    public function isNamed()
    {
        return $this->getSupportParam() === self::NAMED;
    }

    /**
     * 获取向PDOStatement绑定数据的方式
     * @return integer
     */
    public function getSupportParam()
    {
        return $this->_supportParam;
    }

    /**
     * 设置向PDOStatement绑定数据的方式
     * @param integer $value
     * @return \tfc\db\Statement
     */
    public function setSupportParam($value = self::POSITIONAL)
    {
        $value = (int) $value;
        if ($value === self::POSITIONAL || $value === self::NAMED) {
            $this->_supportParam = $value;
        }

        return $this;
    }

    /**
     * 获取PDOStatement类实例
     * @return \PDOStatement
     */
    public function getPDOStatement()
    {
        return $this->_PDOStatement;
    }

    /**
     * 获取PDO方式连接数据库对象
     * 根据需要打开数据库连接，以节省资源
     * @param boolean $autoOpen
     * @return \tfc\db\Driver
     */
    public function getDriver($autoOpen = true)
    {
        return $this->_driver;
    }
}
