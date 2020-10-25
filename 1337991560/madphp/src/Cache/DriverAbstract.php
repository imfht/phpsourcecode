<?php

namespace Madphp\Cache;

/*
* 模板方法模式
*/
abstract class DriverAbstract implements DriverInterface
{

    var $tmp = array();

    // default options, this will be merge to Driver's Options
    var $option = array();

    var $defaultTtl = 157680000; // 3600 * 24 * 365 * 5

    var $fallback = false;
    var $instant;
    var $checked = array();

    /*
     * Basic Functions
     */

    public function set($keyword, $value = "", $time = 0, $option = array())
    {
        if ((Int)$time <= 0) {
            $time = $this->defaultTtl;
        }

        if (Util::$disabled === true) {
            return false;
        }
        $object = array(
            "value" => $value,
            "write_time" => time(),
            "expired_in" => $time,
            "expired_time" => time() + (Int)$time,
        );

        return $this->driverSet($keyword, $object, $time, $option);
    }

    public function get($keyword, $option = array())
    {
        if (Util::$disabled === true) {
            return null;
        }

        $object = $this->driverGet($keyword, $option);

        if ($object == null) {
            return null;
        }

        return isset($option['all_keys']) && $option['all_keys'] ? $object : $object['value'];
    }

    function getInfo($keyword, $option = array())
    {
        $object = $this->driverGet($keyword, $option);

        if ($object == null) {
            return null;
        }
        return $object;
    }

    function delete($keyword, $option = array())
    {
        return $this->driverDelete($keyword, $option);
    }

    function stats($option = array())
    {
        return $this->driverStats($option);
    }

    function clean($option = array())
    {
        return $this->driverClean($option);
    }

    function isExisting($keyword)
    {
        if (method_exists($this, "driverIsExisting")) {
            return $this->driverIsExisting($keyword);
        }

        $data = $this->get($keyword);
        if ($data == null) {
            return false;
        } else {
            return true;
        }
    }

    function search($query)
    {
        if (method_exists($this, "driverSearch")) {
            return $this->driverSearch($query);
        }
        throw new \Exception('Search method is not supported by this driver.');
    }

    function increment($keyword, $step = 1, $option = array())
    {
        $object = $this->get($keyword, array('all_keys' => true));
        if ($object == null) {
            return false;
        } else {
            $value = (Int)$object['value'] + (Int)$step;
            $time = $object['expired_time'] - @date("U");
            $this->set($keyword, $value, $time, $option);
            return true;
        }
    }

    function decrement($keyword, $step = 1, $option = array())
    {
        $object = $this->get($keyword, array('all_keys' => true));
        if ($object == null) {
            return false;
        } else {
            $value = (Int)$object['value'] - (Int)$step;
            $time = $object['expired_time'] - @date("U");
            $this->set($keyword, $value, $time, $option);
            return true;
        }
    }

    /*
     * Extend more time
     */
    function touch($keyword, $time = 300, $option = array())
    {
        $object = $this->get($keyword, array('all_keys' => true));
        if ($object == null) {
            return false;
        } else {
            $value = $object['value'];
            $time = $object['expired_time'] - @date("U") + $time;
            $this->set($keyword, $value, $time, $option);
            return true;
        }
    }

    /*
    * Other Functions Built-int for Cache since 1.3
    */

    public function setMulti($list = array())
    {
        foreach ($list as $array) {
            $this->set($array[0], isset($array[1]) ? $array[1] : 0, isset($array[2]) ? $array[2] : array());
        }
    }

    public function getMulti($list = array())
    {
        $res = array();
        foreach ($list as $array) {
            $name = $array[0];
            $res[$name] = $this->get($name, isset($array[1]) ? $array[1] : array());
        }
        return $res;
    }

    public function getInfoMulti($list = array())
    {
        $res = array();
        foreach ($list as $array) {
            $name = $array[0];
            $res[$name] = $this->getInfo($name, isset($array[1]) ? $array[1] : array());
        }
        return $res;
    }

    public function deleteMulti($list = array())
    {
        foreach ($list as $array) {
            $this->delete($array[0], isset($array[1]) ? $array[1] : array());
        }
    }

    public function isExistingMulti($list = array())
    {
        $res = array();
        foreach ($list as $array) {
            $name = $array[0];
            $res[$name] = $this->isExisting($name);
        }
        return $res;
    }

    public function incrementMulti($list = array())
    {
        $res = array();
        foreach ($list as $array) {
            $name = $array[0];
            $res[$name] = $this->increment($name, $array[1], isset($array[2]) ? $array[2] : array());
        }
        return $res;
    }

    public function decrementMulti($list = array())
    {
        $res = array();
        foreach ($list as $array) {
            $name = $array[0];
            $res[$name] = $this->decrement($name, $array[1], isset($array[2]) ? $array[2] : array());
        }
        return $res;
    }

    public function touchMulti($list = array())
    {
        $res = array();
        foreach ($list as $array) {
            $name = $array[0];
            $res[$name] = $this->touch($name, $array[1], isset($array[2]) ? $array[2] : array());
        }
        return $res;
    }

    /*
     * Config for class
     */
    public function setup($config_name, $value = "")
    {
        if (is_array($config_name)) {
            $this->option = $config_name;
        } else {
            $this->option[$config_name] = $value;
        }
    }

    /*
     * Magic Functions
     */

    function __get($name)
    {
        return $this->get($name);
    }

    function __set($name, $v)
    {
        if (isset($v[1]) && is_numeric($v[1])) {
            return $this->set($name, $v[0], $v[1], isset($v[2]) ? $v[2] : array());
        } else {
            throw new \Exception("Example ->$name = array('VALUE', 300);", 98);
        }
    }

    public function __call($method, $parameters)
    {
        if (!method_exists($this->instant, $method)) {
            throw new \Exception("$method not found!");
        }

        if ($parameters) {
            return call_user_func_array(array($this->instant, $method), $parameters);
        } else {
            return call_user_func(array($this->instant, $method));
        }
    }

    /*
     * Base Functions
     */

    protected function backup()
    {
        return Instance::get(Util::$config['fallback']);
    }

    /*
     * Object for File & SQLite
     */
    protected function encode($data)
    {
        return serialize($data);
    }

    protected function decode($value)
    {
        $x = @unserialize($value);
        if ($x == false) {
            return $value;
        } else {
            return $x;
        }
    }

    /*
     * return System Information
     */
    public function systemInfo()
    {
        $backup_option = $this->option;

        if (count($this->option("system")) == 0) {

            $this->option['system']['storage'] = "file";
            $this->option['system']['storages'] = array();
            $dir = @opendir(dirname(__FILE__) . "/Drivers/");
            if (!$dir) {
                throw new \Exception("Can't open file dir ext", 100);
            }

            while ($file = @readdir($dir)) {
                if ($file != "." && $file != ".." && strpos($file, ".php") !== false) {
                    require_once(dirname(__FILE__) . "/Drivers/" . $file);
                    $namex = str_replace(".php", "", $file);
                    $class = __NAMESPACE__ . "\\Drivers\\" . $namex;
                    $this->option['skipError'] = true;
                    $driver = new $class($this->option);
                    $driver->option = $this->option;
                    if ($driver->checkdriver()) {
                        $this->option['system']['storages'][$namex] = true;
                        $this->option['system']['storage'] = $namex;
                    } else {
                        $this->option['system']['storages'][$namex] = false;
                    }
                }
            }

            if (isset($this->option['system']['storages']['sqlite']) && $this->option['storages']['sqlite'] == true) {
                $this->option['system']['storage'] = "sqlite";
            }
        }

        $this->option("path", self::getPath(true));

        $systemInfo = $this->option;
        $this->option = $backup_option;
        return array('systemInfo' => $systemInfo, 'option' => $this->option);
    }

    function option($name, $value = null)
    {
        if ($value == null) {
            if (isset($this->option[$name])) {
                return $this->option[$name];
            } else {
                return null;
            }
        } else {
            if ($name == "path") {
                $this->checked['path'] = false;
            }

            Util::$config[$name] = $value;
            $this->option[$name] = $value;

            return $this;
        }
    }

    public function setOption($option = array())
    {
        $this->option = array_merge($this->option, Util::$config, $option);
        $this->checked['path'] = false;
    }

    /*
     * return PATH for File & PDO only
     */
    public function getPath($create_path = false)
    {
        return Util::getPath($create_path, $this->option);
    }

    protected function setChmodAuto()
    {
        return Util::setChmodAuto($this->option);
    }

    /**
     * 读取文件
     * @param $file
     * @return string
     * @throws \Exception
     */
    protected function readfile($file)
    {
        if (function_exists("file_get_contents")) {
            return file_get_contents($file);
        } else {
            $string = "";

            $file_handle = @fopen($file, "r");
            if (!$file_handle) {
                throw new \Exception("Can't Read File", 96);
            }
            while (!feof($file_handle)) {
                $line = fgets($file_handle);
                $string .= $line;
            }
            fclose($file_handle);

            return $string;
        }
    }

}