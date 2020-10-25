<?php

namespace Madphp\Cache\Drivers;

use Madphp\Cache\DriverAbstract;

class Predis extends DriverAbstract
{
    var $checkedRedis = false;

    function checkDriver()
    {
        try {
            \Predis\Autoloader::register();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    function __construct($config = array())
    {
        $this->setup($config);
    }

    function connectServer()
    {
        $server = isset($this->option['redis']) ? $this->option['redis'] : array(
            "host" => "127.0.0.1",
            "port" => "6379",
            "password" => "",
            "database" => ""
        );

        if ($this->checkedRedis === false) {

            $c = array(
                "host" => $server['host'],
            );

            $port = isset($server['port']) ? $server['port'] : "";
            $password = isset($server['password']) ? $server['password'] : "";
            $database = isset($server['database']) ? $server['database'] : "";
            $timeout = isset($server['timeout']) ? $server['timeout'] : "";
            $read_write_timeout = isset($server['read_write_timeout']) ? $server['read_write_timeout'] : "";

            $port != "" && $c['port'] = $port;
            $password != "" && $c['password'] = $password;
            $database != "" && $c['database'] = $database;
            $timeout != "" && $c['timeout'] = $timeout;
            $read_write_timeout != "" && $c['read_write_timeout'] = $read_write_timeout;

            $this->instant = new \Predis\Client($c);
            $this->checkedRedis = true;

            if (!$this->instant) {
                $this->fallback = true;
                return false;
            } else {
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
                return $this->instant->setex($keyword, $time, $value);
            } else {
                return $this->instant->setex($keyword, $time, $value);
            }
        } else {
            return $this->backup()->set($keyword, $value, $time, $option);
        }
    }

    function driverGet($keyword, $option = array())
    {
        if ($this->connectServer()) {
            // return null if no caching
            // return value if in caching'
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