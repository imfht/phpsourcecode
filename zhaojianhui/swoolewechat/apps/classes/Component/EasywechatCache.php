<?php

namespace App\Component;

/**
 * easywechat微信缓存类.
 */
class EasywechatCache implements \Doctrine\Common\Cache\Cache
{
    /**
     * 你自己从你想实现的存储方式读取并返回.
     *
     * @param string $id
     *
     * @return bool|string
     */
    public function fetch($id)
    {
        return \Swoole::$php->redis->get($id);
    }

    /**
     * 返回存在与否 bool 值
     *
     * @param string $id
     *
     * @return bool
     */
    public function contains($id)
    {
        return \Swoole::$php->redis->exists($id);
    }

    /**
     * 用你的方式存储该缓存内容即可.
     *
     * @param string $id
     * @param mixed  $data
     * @param int    $lifeTime
     *
     * @return bool
     */
    public function save($id, $data, $lifeTime = 0)
    {
        if ($lifeTime > 0) {
            return \Swoole::$php->redis->setex($id, $lifeTime, $data);
        }

        return \Swoole::$php->redis->set($id, $data, $lifeTime);
    }

    /**
     * 删除并返回 bool 值
     *
     * @param string $id
     *
     * @return int
     */
    public function delete($id)
    {
        return \Swoole::$php->redis->delete($id);
    }

    /**
     * 这个你可以不用实现，返回 null 即可.
     */
    public function getStats()
    {
    }
}
