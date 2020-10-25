<?php

namespace Madphp\Cache\Drivers;

use Madphp\Cache\DriverAbstract;

class Memcache extends DriverAbstract
{

    var $instant;

    function checkDriver()
    {
        // Check memcache
        if (function_exists("\\memcache_connect")) {
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
        if (class_exists("\\Memcache")) {
            $this->instant = new \Memcache();
        } else {
            $this->fallback = true;
        }
    }

    function connectServer()
    {
        $server = $this->option['memcache'];

        if (count($server) < 1) {
            $server = array(
                array("127.0.0.1", 11211),
            );
        }

        foreach ($server as $s) {
            $name = $s[0] . "_" . $s[1];
            if (!isset($this->checked[$name])) {
                try {
                    if (!$this->instant->addServer($s[0], $s[1])) {
                        $this->fallback = true;
                    }
                    $this->checked[$name] = 1;
                } catch (\Exception $e) {
                    $this->fallback = true;
                }
            }
        }
    }

    function driverSet($keyword, $value = "", $time = 300, $option = array())
    {
        $this->connectServer();

        if (isset($option['skipExisting']) && $option['skipExisting'] == true) {
            return $this->instant->add($keyword, $value, false, $time);
        } else {
            if ($time == (3600 * 24 * 365 * 5)) {
                $time = 0;
            }
            return $this->instant->set($keyword, $value, false, $time);
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