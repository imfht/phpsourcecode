<?php


namespace App\Service;


use Predis\Client;

class Redis
{
    public $redis;

    public function __construct(Client $client)
    {
        $this->redis = $client;
    }

    public function set($key, $value)
    {
        $this->redis->set($key, $value);
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }

    public function setex($key, $seconds, $value)
    {
        $this->redis->setex($key, $seconds, $value);
    }

    public function psetex($key, $milliseconds, $value)
    {
        $this->redis->psetex($key, $milliseconds, $value);
    }

    public function exists($key)
    {
        return $this->redis->exists($key);
    }
}