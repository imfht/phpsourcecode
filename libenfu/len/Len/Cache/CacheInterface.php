<?php

namespace Cache;

interface CacheInterface
{
    /**
     * @param $key
     * @return string
     */
    public function get($key);

    /**
     * @param array $key
     * @return array
     */
    public function mget(array $key);

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    public function set($key, $value);

    /**
     * @param array $keys
     * @return bool
     */
    public function mset(array $keys);

    /**
     * @param $key
     * @return bool
     */
    public function del($key);

    /**
     * @param array $keys
     * @return bool
     */
    public function mdel(array $keys);

}