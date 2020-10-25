<?php

namespace Madphp\Cache\Drivers;

use Madphp\Cache\DriverAbstract;

class Memcached extends DriverAbstract
{
    var $instant;

    function checkDriver()
    {
        if (class_exists("\\Memcached")) {
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
        if (class_exists("\\Memcached")) {
            $this->instant = new \Memcached();
        } else {
            $this->fallback = true;
        }
    }

    function connectServer()
    {
        if ($this->checkDriver() == false) {
            return false;
        }

        $s = $this->option['memcache'];
        if (count($s) < 1) {
            $s = array(
                array("127.0.0.1", 11211, 100),
            );
        }

        foreach ($s as $server) {
            $name = isset($server[0]) ? $server[0] : "127.0.0.1";
            $port = isset($server[1]) ? $server[1] : 11211;
            $sharing = isset($server[2]) ? $server[2] : 0;
            $checked = $name . "_" . $port;
            if (!isset($this->checked[$checked])) {
                try {
                    if ($sharing > 0) {
                        if (!$this->instant->addServer($name, $port, $sharing)) {
                            $this->fallback = true;
                        }
                    } else {
                        if (!$this->instant->addServer($name, $port)) {
                            $this->fallback = true;
                        }
                    }
                    $this->checked[$checked] = 1;
                } catch (\Exception $e) {
                    $this->fallback = true;
                }
            }
        }
    }

    function driverSet($keyword, $value = "", $time = 300, $option = array())
    {
        $this->connectServer();
        if (isset($option['isExisting']) && $option['isExisting'] == true) {
            return $this->instant->add($keyword, $value, time() + $time);
        } else {
            return $this->instant->set($keyword, $value, time() + $time);

        }
    }

    function driverGet($keyword, $option = array())
    {
        // return null if no caching
        // return value if in caching
        $this->connectServer();
        $x = $this->instant->get($keyword);
        if ($x == false) {
            return null;
        } else {
            return $x;
        }
    }

    function driverDelete($keyword, $option = array())
    {
        $this->connectServer();
        $this->instant->delete($keyword);
    }

    function driverStats($option = array())
    {
        $this->connectServer();
        $res = array(
            "info" => "",
            "size" => "",
            "data" => $this->instant->getStats(),
        );

        return $res;
    }

    function driverClean($option = array())
    {
        $this->connectServer();
        $this->instant->flush();
    }

    function driverIsExisting($keyword)
    {
        $this->connectServer();
        $x = $this->get($keyword);
        if ($x == null) {
            return false;
        } else {
            return true;
        }
    }
}