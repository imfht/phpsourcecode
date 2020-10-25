<?php

namespace DB;

use MongoDB\Client;

class MongodbModule
{
    /**
     * @var array
     */
    private static $connect_pool = array();

    /**
     * @var \MongoDB\Client
     */
    public $manager;

    /**
     * @var \Exception
     */
    public $connect_exception;

    /**
     * @var \MongoDB\Collection
     */
    public $collection;

    /**
     * @var
     */
    public $debug;

    /**
     * MysqlModule constructor.
     * @param $connect_info
     */
    private function __construct($connect_info)
    {
        list($HOST, $OPTIONS, $DRIVER_OPTIONS, $DBNAME) = static::_createConfig($connect_info);
        try {
            $this->dbname = $DBNAME;
            $this->manager = new Client($HOST, $OPTIONS, $DRIVER_OPTIONS);
        } catch (\Exception $exception) {
            $this->connect_exception = $exception;
            // todo error code ...
            \Output\Error::output(500, $exception->getMessage());
        }
    }

    /**
     * @param $connect_info
     * @return MongodbModule
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
     * @param $collection_name
     * @param array $option
     * @return bool
     */
    public function selectCollection($collection_name, $option = array())
    {
        if (empty($this->manager)) {
            return false;
        }

        try {
            $this->collection = $this->manager->selectCollection($this->dbname, $collection_name, $option);
            $this->debug = $this->manager->__debugInfo();
        } catch (\Exception $exception) {
            $this->connect_exception = $exception;
            // todo error code ...
            \Output\Error::output(500, $exception->getMessage());
        }

        return true;
    }

    /**
     * @param $connect_info
     * @return array
     */
    private static function _createConfig($connect_info)
    {
        $DBNAME = 'test';
        if (empty($connect_info['PARAMETERS'])) {
            $HOST = 'mongodb://' .
                ($connect_info['user'] ? "{$connect_info['user']}" : '') .
                ($connect_info['passwd'] ? ":{$connect_info['passwd']}@" : '') .
                $connect_info['host'] .
                ($connect_info['port'] ? ":{$connect_info['port']}" : '') .
                ($connect_info['dbname'] ? "/{$connect_info['dbname']}" : '');

            $DBNAME = $connect_info['dbname'] ?: $DBNAME;
        } else {
            $PARAMETERS = $connect_info['PARAMETERS'];
            $HOST = 'mongodb://' .
                implode(',', $PARAMETERS['host']) .
                ($PARAMETERS['dbname'] ? "/{$PARAMETERS['dbname']}" : '');

            $DBNAME = $PARAMETERS['dbname'] ?: $DBNAME;
        }
        $DRIVER_OPTIONS = empty($connect_info['DRIVER_OPTIONS']) ? array('typeMap' => array()) : $connect_info['DRIVER_OPTIONS'];
        $DRIVER_OPTIONS['typeMap'] += array(
            'root' => 'array',
            'document' => 'array',
            'array' => 'array'
        );

        !isset($connect_info['OPTIONS']) ? $OPTIONS = [] : $OPTIONS = $connect_info['OPTIONS'];

        return array($HOST, $OPTIONS, $DRIVER_OPTIONS, $DBNAME);
    }

    /**
     * @param $connect_info
     * @return string
     */
    public static function _getConnectTag($connect_info)
    {
        return md5(json_encode($connect_info));
    }
}