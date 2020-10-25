<?php
namespace shiwolang\db;

use PDO;


/**
 * Created by zhouzhongyuan.
 * User: zhou
 * Date: 2015/11/27
 * Time: 10:17
 */
class DB
{
    /**
     * @var DB[]
     */
    private static $_instance = [];

    /**
     * @var Log[]
     */
    private $log = [];

    /**
     * @var null|PDO
     */
    private $pdo = null;
    /**
     * @var int
     */
    private $transactions = 0;


    /**
     * @param array|PDO $config
     * @throws DBException
     */
    private function __construct($config)
    {
        if ($config instanceof PDO) {
            $this->pdo = $config;

            return $this;
        }
        try {
            $commands = isset($config["commands"]) ? $config["commands"] : [];
            $dsn      = '';
            $type     = isset($config["database_type"]) ? $config["database_type"] : "mysql";

            switch ($type) {
                case 'mysql':
                    $dsn        = $type . ':host=' . $config["server"] . (isset($config["port"]) ? ';port=' . $config["port"] : '') . ';dbname=' . $config["database_name"];
                    $commands[] = 'SET SQL_MODE=ANSI_QUOTES';
                    break;
                case 'sqlite':
                    $dsn                = $type . ':' . $config["database_file"];
                    $config["username"] = null;
                    $config["password"] = null;
                    break;
            }
            isset($config["charset"]) && $commands[] = "SET NAMES '" . $config["charset"] . "'";
            $pdo = new PDO(
                $dsn,
                $config["username"],
                $config["password"],
                isset($config["option"]) ? $config["option"] : []
            );

            foreach ($commands as $value) {
                $pdo->exec($value);
            }
            $this->pdo = $pdo;

            return $this;
        } catch (\PDOException $e) {
            throw new DBException($e->getMessage());
        }

    }


    /**
     * 数据库初始化
     *
     * DB::init([
     *     'database_type' => 'mysql',
     *     'database_name' => 'dbutil',
     *     'server'        => 'localhost',
     *     'username'      => 'root',
     *     'password'      => 'root',
     *     'charset'       => 'utf8',
     *     'port'          => '3306',
     *     'option'        => [],
     *     'commands'      => []
     * ]);
     *
     * @param array|PDO $config
     * @param string $name
     * @param bool|false $reinit
     * @return DB
     * @throws DBException
     */
    public static function init($config = [], $name = "default", $reinit = false)
    {
        if (!isset(self::$_instance[$name]) || $reinit) {
            self::$_instance[$name] = new self($config);
        } else {
            throw new DBException("The connection name of (" . $name . ") does exist");
        }

        return self::$_instance[$name];
    }

    /**
     * 获取DB实例
     * @param string $name
     * @return self
     * @throws DBException
     */
    public static function connection($name = "default")
    {
        if (isset(self::$_instance[$name])) {
            return self::$_instance[$name];
        } else {
            throw new DBException("The connection name of (" . $name . ") does not exist");
        }
    }

    /**
     * 获取PDO实例
     * @param string $name
     * @return PDO
     * @throws DBException
     */
    public static function pdo($name = "default")
    {
        if (isset(self::$_instance[$name])) {
            /** @var self $self */
            $self = self::$_instance[$name];

            return $self->getPdo();
        } else {
            throw new DBException("The connection name of (" . $name . ") does not exist");
        }
    }

    /**
     * 获取当前POD实例
     * @return null|PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * 预处理字符串
     *
     * @param $statement
     * @param array $params
     * @param bool $getRawSql
     * @param null $result
     * @return Statement|String
     * @internal param bool $execute
     */
    public function prepare($statement, $params = [], $getRawSql = false, &$result = null)
    {
        if ($statement instanceof StatementBuilderInterface) {
            $params    = $statement->getPrepareStatementParams();
            $statement = $statement->getPrepareStatement();
        }
        $_statement = $this->getPdo()->prepare($statement);

        $container = new Statement($_statement, $this);

        return !$getRawSql ? $container->execute($params, $result) : (new Log($this, $statement, $params))->getRawSql();
    }

    /**
     * 查询预处理字符串
     *
     * @param $statement
     * @param array $params
     * @param bool $getRawSql
     * @param null $result
     * @return Statement|String
     */
    public function query($statement, $params = [], $getRawSql = false, &$result = null)
    {
        return $this->prepare($statement, $params, $getRawSql, $result);
    }

    /**
     * 单表插入
     *
     * @param $tableName
     * @param $params
     * @param null $lastInsertIdName
     * @return string
     */
    public function insert($tableName, $params, $lastInsertIdName = null)
    {
        $keys    = array_keys($params);
        $sql     = str_replace(["replace_tableName", "replace_cols", "replace_values"], [
            $tableName,
            "`" . implode("`,`", $keys) . "`",
            ":" . implode(",:", $keys)
        ], "INSERT INTO `replace_tableName` (replace_cols) VALUES(replace_values)");
        $_params = [];
        foreach ($params as $name => $value) {
            $_params[":" . $name] = $value;
        }
        $this->prepare($sql, $_params);

        return $lastInsertIdName === null ? $this->pdo->lastInsertId() : $this->pdo->lastInsertId($lastInsertIdName);
    }

    /**
     * 单表更新
     *
     * @param $tableName
     * @param $params
     * @param $where
     * @param $whereParams
     * @return int
     */
    public function update($tableName, $params, $where, $whereParams)
    {
        $keys = array_keys($params);
        if (!isset($whereParams[0])) {
            $where = str_replace(array_keys($whereParams), array_fill(0, count($whereParams), '?'), $where);
        }
        $sql     = str_replace(["replace_tableName", "replace_value = ''", "replace_where"], [
            $tableName,
            "`" . implode("`=?, `", $keys) . "`=?",
            $where
        ], "UPDATE `replace_tableName` SET replace_value = '' WHERE replace_where");
        $_params = [];
        foreach (array_merge($params, $whereParams) as $name => $value) {
            $_params[] = $value;
        }

        return $this->prepare($sql, $_params)->rowCount();
    }

    /**
     * 单表删除
     *
     * @param $tableName
     * @param $where
     * @param $whereParams
     * @return int
     */
    public function delete($tableName, $where, $whereParams)
    {
        $sql = str_replace(["replace_tableName", "replace_where"], [
            $tableName,
            $where
        ], "DELETE FROM `replace_tableName` WHERE replace_where");

        return $this->prepare($sql, $whereParams)->rowCount();
    }

    /**
     * 可嵌套事务处理，开始事务
     */
    public function beginTransaction()
    {
        ++$this->transactions;

        if ($this->transactions == 1) {
            $this->pdo->beginTransaction();
        }
    }

    /**
     * 可嵌套事务处理，回滚事务
     */
    public function rollBack()
    {
        if ($this->transactions == 1) {
            $this->transactions = 0;
            $this->pdo->rollBack();
        } else {
            --$this->transactions;
        }
    }

    /**
     * 可嵌套事务处理，提交事务
     */
    public function commit()
    {
        if ($this->transactions == 1) {
            $this->pdo->commit();
        }
        --$this->transactions;
    }

    /**
     * 可嵌套事务处理，回调方式
     * @param callable $fn
     * @throws \Exception
     */
    public function transaction(callable $fn)
    {
        $this->beginTransaction();
        try {
            $fn($this);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * 获取sql执行记录
     * @param null|int $index
     * @return Log[]|Log
     */
    public function getLog($index = null)
    {
        return $index === null ? $this->log : $this->log[$index];
    }

    /**
     * @param Log[] $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * @param Log $log
     */
    public function appendLog(Log $log)
    {
        $this->log[] = $log;
    }

    /**
     * 获取最后sql执行记录
     * @return Log
     */
    public function getLastLog()
    {
        return end($this->log);
    }

    /**
     * 清除所有LOG
     */
    public function clearLog()
    {
        $this->log = [];
    }
}
