<?php

namespace Madphp\Db\Pdo\Engine;

use Madphp\Config;
use Madphp\Db\Pdo\Connection;

class Mysql extends Connection
{

    var $instance;
    var $dbname;
    var $config;

    public static $connections = array();

    /**
     * 属性
     * @var array
     */
    private $_attribute = array(

        \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
        \PDO::ATTR_STRINGIFY_FETCHES => false,
    );

    function __construct($dbname, $config)
    {
        if (!$this->checkDriver()) {
            throw new \Exception('Can not find mysql pdo driver.');
        }
        if (isset($config['debug'])) {
            $this->debug = $config['debug'];
        }
        $this->dbname = $dbname;
        $this->config = $config;
    }

    function checkDriver()
    {
        $drivers = pdo_drivers();
        if (array_search('mysql', $drivers) === FALSE) {
            return false;
        }
        return TRUE;
    }

    function connect($isWrite = false)
    {
        $this->modality = $this->isWrite ? self::MODALITY_WRITE : ($isWrite ? self::MODALITY_WRITE : self::MODALITY_READ);
        $connection_key = 'pdo_mysql_' . $this->dbname . '_' . $this->modality . '_';
        if (isset(self::$connections[$connection_key])) {
            return self::$connections[$connection_key];
        }

        $server = Config::get('db', 'mysql');
        $dbConfig = Config::get('db', 'database,' . $this->dbname);
        $dbModalityConfig = self::getModalityConfig($dbConfig);
        // 置空连接方式
        $this->modality = null;
        $this->resetIsWrite();

        $charset = isset($server['charset']) ? $server['charset'] : 'UTF8';
        $dbname = isset($dbConfig['dbname']) ? $dbConfig['dbname'] : $this->dbname;
        $dsn = 'mysql:host=' . $dbModalityConfig['host'] . ';port=' . $dbModalityConfig['port'] . ';dbname=' . $dbname;
        $options = array(
            \PDO::ATTR_TIMEOUT => isset($server['timeout']) ? $server['timeout'] : 3,
            \PDO::ATTR_PERSISTENT => isset($server['persistent']) ? $server['persistent'] : false,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $charset,
        );
        try {

            self::$connections[$connection_key] = new \PDO(
                $dsn, $dbModalityConfig['username'], $dbModalityConfig['password'], $options
            );

            foreach ($this->_attribute as $key => $val) {
                self::$connections[$connection_key]->setAttribute($key, $val);
            }
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        return self::$connections[$connection_key];
    }

    private function getModalityConfig($dbConfig)
    {
        $dbConfig = $dbConfig[$this->modality];
        shuffle($dbConfig['servers']);
        $dbServer = $dbConfig['servers'][0];
        list($dbConfig['host'], $dbConfig['port']) = explode(':', $dbServer);
        return $dbConfig;
    }
}