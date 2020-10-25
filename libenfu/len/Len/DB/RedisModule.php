<?php

namespace DB;

use Predis\Client;
use Predis\Connection\ConnectionException;

abstract class RedisModule
{
    /**
     * @var bool|Client
     */
    protected $redis;

    /**
     * @var Client[]
     */
    private static $connect_pool = [];

    /**
     * RedisModule constructor.
     * @param $redis_info
     */
    public function __construct($redis_info)
    {
        $this->redis = static::instance($redis_info);
    }

    /**
     * @param $redis_info
     * @return bool|Client
     * @throws \Exception
     */
    public static function instance($redis_info)
    {
        $connect_flag = md5(json_encode($redis_info));
        if (!empty(static::$connect_pool[$connect_flag])) {
            try {
                static::$connect_pool[$connect_flag]->isConnected();
                return static::$connect_pool[$connect_flag];
            } catch (ConnectionException $error) {
                unset(static::$connect_pool[$connect_flag]);
            }
        }
        $connect = false;
        if (empty($redis_info['PARAMETERS'])) {
            $connect = static::connect_redis($redis_info);
        }

        if (empty($redis_info['OPTION']) && !empty($redis_info['PARAMETERS'])) {
            $connect = static::connect_redis($redis_info['PARAMETERS']);
        } else if (isset($redis_info['OPTION']) && is_array($redis_info['OPTION']) && count($redis_info['OPTION']) > 0) {
            $connect = static::connect_redis($redis_info['PARAMETERS'], $redis_info['OPTION']);
        }

        if (false === $connect) {
            throw new \Exception('connect info error');
        }

        static::$connect_pool[$connect_flag] = $connect;

        return $connect;
    }

    /**
     * @param $redis_info
     */
    public static function destructRedis($redis_info)
    {
        $connect_flag = md5(json_encode($redis_info));
        unset(static::$connect_pool[$connect_flag]);
    }

    /**
     * @param $redis_info
     * @param null $option
     * @return Client
     * @throws \Exception
     */
    public static function connect_redis($redis_info, $option = null)
    {
        if ($option == null) {
            $redis = new Client($redis_info);
        } else {
            $redis = new Client($redis_info, $option);
        }

        if (!$redis) {
            throw new \Exception('can not connected redis');
        }

        return $redis;
    }

    public function disconnect()
    {
        @$this->redis->disconnect();
    }

}