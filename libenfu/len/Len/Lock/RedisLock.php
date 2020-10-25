<?php

namespace Lock;

use Handler\RedisHandler;

class RedisLock
{
    const KEY_LOCK = 'lock:%s:%s';

    /**
     * @var string
     */
    private $lockey = '';

    /**
     * redisLock constructor.
     * @param $tag
     * @param $uniqid
     */
    private function __construct($tag, $uniqid)
    {
        $this->lockey = sprintf(static::KEY_LOCK, $tag, $uniqid);
    }

    /**
     * @param $tag
     * @param $uniqid
     * @return bool
     */
    public static function instance($tag, $uniqid)
    {
        $self = new self($tag, $uniqid);

        return $self->lock();
    }

    /**
     * @return bool
     */
    public function lock()
    {
        $incr = RedisHandler::getRedisLock()->incr($this->lockey);
        if ($incr != 1) {
            return false;
        }
        RedisHandler::getRedisLock()->expire($this->lockey, 10);
        register_shutdown_function(array($this, 'unlock'));

        return true;
    }

    /**
     * @return mixed
     */
    public function unlock()
    {
        return RedisHandler::getRedisLock()->pexpire($this->lockey, 1);
    }
}