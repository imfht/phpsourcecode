<?php

namespace DB;

class MysqlModule
{
    /**
     * @var array
     */
    private $connect_info;

    /**
     * @var \PDO
     */
    private $connect_handler;

    /**
     * @var \PDOException
     */
    private $connect_exception;

    /**
     * @var \PDOStatement
     */
    private $statement;

    /**
     * @var array
     */
    static $connect_pool = array();

    /**
     * MysqlModule constructor.
     * @param $connect_info
     */
    private function __construct($connect_info)
    {
        try {
            $this->connect_info = static::filterConfig($connect_info);
            $this->connect_handler = static::connect($this->connect_info);
        } catch (\PDOException $exception) {
            $this->connect_exception = $exception;
            // todo error code ...
            \Output\Error::output(500, $exception->getMessage());
        }
    }

    /**
     * @param $connect_info
     * @return MysqlModule
     */
    public static function instance($connect_info)
    {
        $tag = static::_getConnectTag($connect_info);
        if (empty(static::$connect_pool[$tag])) {
            static::$connect_pool[$tag] = new self($connect_info);
        }

        return static::$connect_pool[$tag];
    }

    /**
     * @param $config
     * @return string
     */
    public static function _getConnectTag($config)
    {
        return md5($config['host'] . $config['port'] . $config['user'] . $config['passwd']);
    }

    /**
     * @param $config
     * @return \PDO
     */
    public static function connect($config)
    {
        $options = array(
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        );

        return new \PDO($config['dsn'], $config['user'], $config['passwd'], $options);
    }

    /**
     * @param $connect_info
     * @return array
     */
    private static function filterConfig($connect_info)
    {
        if (empty($connect_info['host'])) {
            $connect_info['host'] = '127.0.0.1';
        }
        if (empty($connect_info['user'])) {
            $connect_info['user'] = 'root';
        }
        if (empty($connect_info['passwd'])) {
            $connect_info['passwd'] = '';
        }
        if (empty($connect_info['chatset'])) {
            $connect_info['charset'] = 'UTF8';
        }
        if (empty($connect_info['port'])) {
            $connect_info['port'] = 3306;
        }

        $dsn = 'mysql:host=' . $connect_info['host'] . ';port=' . $connect_info['port'] . ';charset=' . $connect_info['charset'];
        if (!empty($connect_info['dbname'])) {
            $dsn .= ';dbname=' . $connect_info['dbname'];
        }

        return array(
            'dsn' => $dsn,
            'user' => $connect_info['user'],
            'passwd' => $connect_info['passwd']
        );
    }

    /**
     * @param $query
     */
    public function query($query)
    {
        $this->statement = $this->connect_handler->prepare($query);
    }

    /**
     * @param $param
     * @param $value
     * @param null $type
     */
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = \PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = \PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = \PDO::PARAM_NULL;
                    break;
                default:
                    $type = \PDO::PARAM_STR;
            }
        }

        $this->statement->bindValue($param, $value, $type);
    }

    /**
     * @param $array
     */
    public function bindArray($array)
    {
        foreach ($array as $field => $value) {
            $this->bind($field, $value);
        }
    }

    /**
     * @return bool
     */
    public function execute()
    {
        return $this->statement->execute();
    }

    /**
     * @return array
     */
    public function fetchAll()
    {
        $this->execute();
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @return mixed
     */
    public function fetch()
    {
        $this->execute();
        return $this->statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @return int
     */
    public function rowCount()
    {
        return $this->statement->rowCount();
    }

    /**
     * @return string
     */
    public function lastInsertId()
    {
        return $this->connect_handler->lastInsertId();
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->connect_handler->beginTransaction();
    }

    /**
     * @return bool
     */
    public function commit()
    {
        return $this->connect_handler->commit();
    }

    /**
     * @return bool
     */
    public function rollBack()
    {
        return $this->connect_handler->rollBack();
    }

    /**
     * @return bool
     */
    public function debugDumpParams()
    {
        return $this->statement->debugDumpParams();
    }

    /**
     * @return \PDOException
     */
    public function getConnectException()
    {
        return $this->connect_exception;
    }

}