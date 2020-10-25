<?php
namespace workerbase\classs\datalevels;

/**
 * 关系型数据库Dao实现
 * @author fukaiyao
 *
 */
abstract class BaseRdbDao
{
    /**
     * BaseRdbDao constructor.
     * @throws \Exception
     */
    public function __construct() 
    {
        $this->_dbConnName = $this->dbConnName();
        $dbConnName = $this->_dbConnName;

        $this->_db = Db::getInstance($dbConnName);

        $this->_tableName  = $this->tableName();
        if ($this->_tableName == null) {
            throw new DaoException("rdb table name not config.");
        }
        $pk = $this->primaryKey();
        if (!empty($pk)) {
            $this->_pk = $pk;
        }
    }
    
    /**
     * 执行非查询sql语句，例: insert, update delete
     * @param string $sql           - sql语句
     * @param array $params    - sql绑定参数
     * @return int  - 返回执行语句影响的行数
     */
    protected function executeSql($sql, $params = array())
    {
        return $this->_db->pdo->prepare($sql)->execute($params);
    }
    
    /**
     * 执行sql查询
     * @param string $sql           - sql语句
     * @param array $params    - sql绑定参数
     * @return array    - 返回查询结果数组，找不到返回空数组
     */
    protected function queryAllBySql($sql, $params = array())
    {
        return $this->_db->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * 执行sql查询
     * @param string $sql           - sql语句
     * @param array $params    - sql绑定参数
     * @return array    - 返回查询结果数组，找不到返回空数组
     */
    protected function queryRowBySql($sql, $params = array())
    {
        $forStr = '';
        $sql = strtolower($sql);
        $forPos = strpos($sql, 'for');
        if (false !== $forPos) {
            $forStr = substr($sql, $forPos, strlen($sql));
            $sql = substr($sql, 0, $forPos);
        }

        $res = preg_match_all('/[\s\S]*(limit\s+\d{1})[\s\S]*/', $sql,$result);
        if ($res) {
            $sql = str_replace($result[1], 'limit 1', $sql);
        } else {
            $sql = $sql . ' limit 1 ';
        }

        $sql = $sql . $forStr;
        return $this->_db->query($sql, $params)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param \PDOStatement $statement
     * @param array $params  参数绑定格式 [':id' => 1]
     */
    protected function bindParams(\PDOStatement $statement, array $params)
    {
        foreach ($params as $key => $value)
        {
            $val = $this->typeMap($value, gettype($value));
            $statement->bindValue($key, $val[ 0 ], $val[ 1 ]);
        }
    }

    protected function typeMap($value, $type)
    {
        $map = [
            'NULL' => \PDO::PARAM_NULL,
            'integer' => \PDO::PARAM_INT,
            'double' => \PDO::PARAM_STR,
            'boolean' => \PDO::PARAM_BOOL,
            'string' => \PDO::PARAM_STR,
            'object' => \PDO::PARAM_STR,
            'resource' => \PDO::PARAM_LOB
        ];

        if ($type === 'boolean')
        {
            $value = ($value ? '1' : '0');
        }
        elseif ($type === 'NULL')
        {
            $value = null;
        }

        return [$value, $map[ $type ]];
    }

    /**
     * 获取db对象
     * @param bool $isMaster - 是否主库
     * @return null|Db
     */
    protected function getDb($isMaster = true)
    {
        $db = $this->_db;
        if (!$isMaster) {
            //从库
            $db = Db::getInstance('slaveDb');
        }
        return $db;
    }
    
    /**
     * 设置表名tablePrefix
     */
    abstract protected  function tableName();

    /**
     * 数据库连接名
     * @return string
     */
    protected function dbConnName()
    {
        return 'db';
    }
    
    /**
     * 返回表名
     * @return string
     */
    protected function getTableName() 
    {
        return $this->_tableName;
    }
    
    /**
     * 设置主键
     */
    protected function primaryKey()
    {
        return 'id';    
    }

    /**
     * 解析表名，主要处理表前缀
     * @param string $tableName     - 表名
     * @return string       - 处理后的表名
     * @throws DaoException
     */
    private function parserTableName($tableName)
    {
        $p = '/\{\{([\-_0-9a-zA-Z]+)\}\}/';
        $result = null;
        $ret = preg_match($p, $tableName, $result);
        if (!$ret) {
            throw new DaoException("Incorrect table name \"$tableName\"");
        }

        $options = loadc('config')->get($this->_dbConnName, "config");
        if (isset($options['prefix'])) {
            return $options['prefix'] . $tableName;
        }

        return $tableName;
    }

    /**
     * @var null|Db
     */
    private $_db = null;
    
    /**
     * 表名
     * @var string
     */
    protected  $_tableName = null;

    /**
     * 数据库连接名
     * @var string
     */
    protected $_dbConnName = null;
    
    /**
     * 主键
     * @var string
     */
    protected $_pk = 'id';
}