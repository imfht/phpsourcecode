<?php

namespace Madphp\Db\Pdo;

abstract class Connection implements PdoInterface
{

    /**
     * @var \PDOStatement
     */
    protected $_PDOStatement;

    /**
     * @var \PDO
     */
    protected $_PDOConn;

    protected $queryCount = 0;

    /**
     * 是否选择主数据库
     * @var false
     */
    protected $isWrite = false;

    /**
     * 连接方式
     * @var null
     */
    protected $modality = null;

    /**
     * 写模式
     */
    const MODALITY_WRITE = 'write';

    /**
     * 读取模式
     */
    const MODALITY_READ = 'read';

    /**
     * 是否Debug
     * @var bool
     */
    protected $debug = false;

    /**
     * The default fetch style of the connection.
     *
     * @var int
     */
    protected $fetchStyle = \PDO::FETCH_ASSOC;

    /**
     * 数据类型
     * @var array
     */
    protected $paramType = array(
        'bool' => \PDO::PARAM_BOOL,
        'boolean' => \PDO::PARAM_BOOL,
        'null' => \PDO::PARAM_NULL,
        'integer' => \PDO::PARAM_INT,
        'int' => \PDO::PARAM_INT,
        'string' => \PDO::PARAM_STR,
        'stmt' => \PDO::PARAM_STMT,
        'object' => \PDO::PARAM_LOB,
        'float' => \PDO::PARAM_STR,
        'double' => \PDO::PARAM_STR,
        'output' => \PDO::PARAM_INPUT_OUTPUT
    );

    /**
     * 检测驱动是否存在
     * @return mixed
     */
    abstract public function checkDriver();

    /**
     * 数据库连接
     * @return mixed
     */
    abstract public function connect();

    /**
     * 设置属性
     * @param $attribute
     * @param $value
     * @return $this
     */
    public function setAttribute($attribute, $value)
    {
        $this->_attribute[$attribute] = $value;
        return $this;
    }

    /**
     * 连接主服务器
     * @return $this
     */
    public function setIsWrite()
    {
        $this->isWrite = true;
        return $this;
    }

    /**
     * 取消连接主服务器
     * @return $this
     */
    public function resetIsWrite()
    {
        $this->isWrite = false;
        return $this;
    }

    /**
     * 设置是否Debug
     * @param bool|true $is
     * @return $this
     */
    public function setDebug($is = true)
    {
        $this->debug = $is;
        return $this;
    }

    /**
     * Get the fetch style for the connection.
     * @return int
     */
    public function getFetchStyle()
    {
        return $this->fetchStyle;
    }

    /**
     * Set the default fetch style for the connection.
     * @param $fetchStyle
     * @return $this
     */
    public function setFetchStyle($fetchStyle)
    {
        $this->fetchStyle = $fetchStyle;
        return $this;
    }

    /**
     * 插入数据
     * @param $sql
     * @param array $parameterMap
     * @param array $sqlMap
     * @return $this
     */
    public function insert($sql, array $parameterMap = array(), array $sqlMap = array())
    {
        $this->trace();
        $this->setIsWrite()->getStatement($sql, $sqlMap, $parameterMap);
        return $this;
    }

    /**
     * 更新数据
     * @param $sql
     * @param array $sqlMap
     * @param array $parameterMap
     * @return $this
     */
    public function update($sql, $sqlMap = array(), $parameterMap = array())
    {
        $this->trace($sql, $sqlMap, $parameterMap);
        $this->setIsWrite()->getStatement($sql, $sqlMap, $parameterMap);
        return $this;
    }

    /**
     * 删除数据
     * @param $sql
     * @param array $sqlMap
     * @param array $parameterMap
     * @return $this
     */
    public function delete($sql, $sqlMap = array(), $parameterMap = array())
    {
        $this->trace($sql, $sqlMap, $parameterMap);
        $this->setIsWrite()->getStatement($sql, $sqlMap, $parameterMap);
        return $this;
    }

    /**
     * 原生Query
     * @param $sql
     * @return mixed
     */
    public function query($sql)
    {
        $this->trace($sql, array(), array());
        return $this->setIsWrite()->connect()->query($sql);
    }

    /**
     * 执行插入修改或删除
     * @param $sql
     * @return mixed
     */
    public function exec($sql)
    {
        $this->trace($sql, array(), array());
        return $this->setIsWrite()->connect()->exec($sql);
    }

    /**
     * 取回结果集中所有字段的值,作为关联数组返回  第一个字段作为码
     * @param $sql
     * @param array $sqlMap
     * @param array $parameterMap
     * @return array
     */
    public function fetchAll($sql, $sqlMap = array(), $parameterMap = array())
    {
        $this->trace($sql, $sqlMap, $parameterMap);
        $sth = $this->getStatement($sql, $sqlMap, $parameterMap);
        $data = $sth->fetchAll($this->getFetchStyle());
        return $data;
    }

    /**
     * Run a select statement and return a single result.
     *
     * @param  string $sql
     * @param  array $sqlMap
     * @param  array $parameterMap
     * @return mixed
     */
    public function fetchOne($sql, $sqlMap = array(), $parameterMap = array())
    {
        $data = $this->fetchAll($sql, $sqlMap, $parameterMap);
        return count($data) > 0 ? reset($data) : null;
    }

    /**
     * @param  string $sql
     * @param  int $columnNumber
     * @param  array $sqlMap
     * @param  array $parameterMap
     * @return mixed
     */
    public function fetchColumn($sql, $columnNumber = 0, $sqlMap = array(), $parameterMap = array())
    {
        $data = array();
        $this->trace($sql, $sqlMap, $parameterMap);
        $sth = $this->getStatement($sql, $sqlMap, $parameterMap);
        while ($row = $sth->fetchColumn($columnNumber)) {
            $data[] = $row;
        }

        return $data ? $data : false;
    }

    /**
     * 获取插入的ID
     * @param null $name
     * @return int
     */
    public function lastInsertId($name = null)
    {
        return $this->_PDOConn->lastInsertId($name);
    }

    /**
     * 在一个多行集语句句柄中推进到下一个行集
     * @return bool
     */
    public function nextRowset()
    {
        return $this->_PDOStatement->nextRowset();
    }

    /**
     * 获取更新数量
     * @return int
     */
    public function rowCount()
    {
        return $this->_PDOStatement->rowCount();
    }

    /**
     * 状态
     * @return bool
     */
    public function status()
    {
        return $this->_PDOStatement->errorCode() === '00000';
    }

    /**
     * 获取statement Error Code
     * @return string
     */
    public function statementErrorCode()
    {
        return $this->_PDOStatement->errorCode();
    }

    /**
     * 获取statement Error Info
     * @return array
     */
    public function statementErrorInfo()
    {
        return $this->_PDOStatement->errorInfo();
    }

    /**
     * 获取pdo error code
     * @return mixed
     */
    public function pdoErrorCode()
    {
        return $this->_PDOConn->errorCode();
    }

    /**
     * 获取PDO Error Info
     * @return array
     */
    public function pdoErrorInfo()
    {
        return $this->_PDOConn->errorInfo();
    }

    /**
     * 检查是否在一个事务内
     * @return bool
     * @throws Exception
     */
    public function inTransaction()
    {
        $this->_PDOConn = $this->connect();
        return $this->_PDOConn->inTransaction();
    }

    /**
     * 开启事务
     * @throws Exception
     */
    public function beginTransaction()
    {
        $this->_PDOConn = $this->connect();
        $this->_PDOConn->beginTransaction();
    }

    /**
     * 提交事务
     * @throws Exception
     */
    public function commit()
    {
        $this->_PDOConn = $this->connect();
        $this->_PDOConn->commit();
    }

    /**
     * 回滚
     * @throws Exception
     */
    public function rollBack()
    {
        $this->_PDOConn = $this->connect();
        $this->_PDOConn->rollBack();
    }

    /**
     * @param $sql
     * @param $sqlMap
     * @param $parameterMap
     * @return \PDOStatement
     * @throws Exception
     */
    protected function getStatement($sql, $sqlMap, $parameterMap)
    {
        $this->sql = $sql;
        $this->parameterMap = $parameterMap;
        $this->sqlMap = $sqlMap;

        $sql = $this->sqlMap($sql, $sqlMap);
        $this->_PDOConn = $conn = $this->connect();
        $this->_PDOStatement = $sth = $conn->prepare($sql);
        $this->bindValues($parameterMap, $sth);
        $sth->execute();
        return $sth;
    }

    /**
     * @param $sql
     * @param array $sqlMap
     * @return string
     * @throws Exception
     */
    protected function sqlMap($sql, array $sqlMap)
    {
        $replacePairs = array();
        foreach ($sqlMap as $key => $value) {
            if (!is_string($value)) {
                throw new \Exception('替换参数值必须是字符串。');
            }
            $replacePairs["#" . $key . "#"] = $value;
        }
        return strtr($sql, $replacePairs);
    }

    /**
     * 绑定数据
     * @param array $params
     * @param \PDOStatement $sth
     * @throws Exception
     */
    protected function bindValues(array $params, \PDOStatement $sth)
    {
        foreach ($params as $parameter => $value) {
            if (is_array($value) || is_object($value)) {
                throw new \Exception('Sql绑定参数不能为数组或对象.');
            }
            $dataType = $this->paramType[strtolower(gettype($value))];
            $sth->bindValue($parameter, $value, $dataType);
        }

    }

    /**
     * debug
     * @param $sql
     * @param array $sqlMap
     * @param array $parameterMap
     * @return mixed|string
     * @throws Exception
     */
    public function debug($sql, $sqlMap = array(), $parameterMap = array())
    {
        $sql = $this->sqlMap($sql, $sqlMap);
        if (strstr($sql, ':')) {
            $matches_s = array();
            foreach ($parameterMap as $key => $val) {
                if (is_string($val)) {
                    $val = "'{$val}'";
                }
                $matches_s[':' . $key] = $val;
            }
            $asSql = strtr($sql, $matches_s);
        } else {
            $asSql = $sql;
            foreach ($parameterMap as $val) {
                $strPos = strpos($asSql, '?');
                if (is_string($val)) {
                    $val = "'{$val}'";
                }
                $asSql = substr_replace($asSql, $val, $strPos, 1);
            }
        }
        console(array('Query' => $this->queryCount, 'sql' => $asSql,));
        return $asSql;
    }

    /**
     * 跟踪SQL执行
     * @param string $sql
     * @param array $sqlMap
     * @param array $parameterMap
     */
    protected function trace($sql = '', $sqlMap = array(), $parameterMap = array())
    {
        $this->queryCount++;
        if ($this->debug) {
            $this->debug($sql, $sqlMap, $parameterMap);
        }
    }
}