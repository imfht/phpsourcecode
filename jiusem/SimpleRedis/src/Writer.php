<?php namespace Crazymus\SimpleRedis;

/*
 * This file is part of the SimpleRedis package.
 *
 * (c) Jingzhou Guo <crazymus@foxmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Writer
{
    /**
     * TCP连接句柄
     * @var resource
     */
    protected $handler;

    public function __construct($handler)
    {
        $this->handler = $handler;
    }

    /**
     * 通过TCP连接发送命令
     *
     * @param $cmd
     * @return bool|int
     */
    public function send($cmd)
    {
        $result = fwrite($this->handler, $cmd, strlen($cmd));
        return $result;
    }
}
