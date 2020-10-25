<?php

namespace Cache;

use Yac;

class YacCache implements CacheInterface
{

    /**
     * @var YacCache
     */
    static $interface = null;

    /**
     * @var Yac
     */
    private $yac = null;

    /**
     * @param string $prefix
     * @return YacCache
     */
    public static function instance($prefix = '')
    {
        if (self::$interface instanceof self) {
            return self::$interface;
        }

        return self::$interface = new self($prefix);
    }

    /**
     * YacCache constructor.
     * @param $prefix
     * @throws \Exception
     */
    public function __construct($prefix)
    {
        if (!extension_loaded('yac')) {
            throw new \Exception('YAC extension does not exist');
        }
        $this->yac = new Yac($prefix);
    }

    /**
     * @param $key
     * @return string
     */
    public function get($key)
    {
        return $this->yac->get($key);
    }

    /**
     * @param array $keys
     * @return string
     */
    public function mget(array $keys)
    {
        return $this->yac->get($keys);
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    public function set($key, $value)
    {
        return $this->yac->add($key, $value);
    }

    /**
     * @param $keys
     * @return bool
     */
    public function mset(array $keys)
    {
        return $this->yac->set($keys);
    }

    /**
     * @param $key
     * @return bool
     */
    public function del($key)
    {
        return $this->yac->delete($key);
    }

    /**
     * @param array $keys
     * @return bool
     */
    public function mdel(array $keys)
    {
        return $this->yac->delete($keys);
    }

    /**
     * @return bool
     */
    public function flush()
    {
        return $this->yac->flush();
    }

    /**
     * @return array
     */
    public function dump()
    {
        return $this->yac->dump();
    }
}