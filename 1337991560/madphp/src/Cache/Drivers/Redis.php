<?php

namespace Madphp\Cache\Drivers;

use Madphp\Cache\DriverAbstract;

class Redis extends DriverAbstract
{

    var $checkedRedis = false;

    function checkDriver()
    {
        // Check Redis
        if (class_exists("\\Redis")) {
            return true;
        }
        $this->fallback = true;
        return false;
    }

    function __construct($config = array())
    {
        $this->setup($config);
        if (!$this->checkDriver() && !isset($config['skipError'])) {
            $this->fallback = true;
        }

        $this->instant = new \Redis();
        $this->connectServer();
    }

    function connectServer()
    {
        if ($this->checkedRedis === false) {

            $defaultRedisServer = array(
                "host" => "127.0.0.1",
                "port" => "6379",
                "database" => "",
                "password" => "",
                "timeout" => "0",
            );
            $server = isset($this->option['redis']) ? $this->option['redis'] : $defaultRedisServer;
            $host = $server['host'];
            $port = isset($server['port']) ? (Int)$server['port'] : "";
            $database = isset($server['database']) ? $server['database'] : "";
            $password = isset($server['password']) ? $server['password'] : "";
            $timeout = isset($server['timeout']) ? $server['timeout'] : "";

            if (!$this->instant->connect($host, (int)$port, (Int)$timeout)) {
                $this->checkedRedis = true;
                $this->fallback = true;
                return false;
            } else {
                if ($database != "") {
                    $this->instant->select((Int)$database);
                }
                $this->checkedRedis = true;
                return true;
            }
        }

        return true;
    }

    function driverSet($keyword, $value = "", $time = 300, $option = array())
    {
        if ($this->connectServer()) {
            $value = $this->encode($value);
            if (isset($option['skipExisting']) && $option['skipExisting'] == true) {
                return $this->instant->set($keyword, $value, array('xx', 'ex' => $time));
            } else {
                return $this->instant->set($keyword, $value, $time);
            }
        } else {
            return $this->backup()->set($keyword, $value, $time, $option);
        }
    }

    function driverGet($keyword, $option = array())
    {
        if ($this->connectServer()) {
            // return null if no caching
            // return value if in caching
            $x = $this->instant->get($keyword);
            if ($x == false) {
                return null;
            } else {
                return $this->decode($x);
            }
        } else {
            $this->backup()->get($keyword, $option);
        }
    }

    function driverDelete($keyword, $option = array())
    {
        if ($this->connectServer()) {
            $this->instant->delete($keyword);
        }
    }

    function driverStats($option = array())
    {
        if ($this->connectServer()) {
            $res = array(
                "info" => "",
                "size" => "",
                "data" => $this->instant->info(),
            );
            return $res;
        }

        return array();
    }

    function driverClean($option = array())
    {
        if ($this->connectServer()) {
            $this->instant->flushDB();
        }
    }

    function driverIsExisting($keyword)
    {
        if ($this->connectServer()) {
            $x = $this->instant->exists($keyword);
            if ($x == null) {
                return false;
            } else {
                return true;
            }
        } else {
            return $this->backup()->isExisting($keyword);
        }
    }
}