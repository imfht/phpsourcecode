<?php namespace Crazymus\SimpleRedis;

/*
 * This file is part of the SimpleRedis package.
 *
 * (c) Jingzhou Guo <crazymus@foxmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Command
{
    /**
     * 根据Redis协议拼接的请求内容
     *
     * @var string
     */
    protected $stream;

    /**
     * Redis命令组成的数组
     *
     * @var array
     */
    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
        $this->toStream();
    }

    /**
     * 获取请求内容
     *
     * @return string
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * 生成请求内容
     */
    protected function toStream()
    {
        $count = count($this->params);
        $buffer = '*' . $count . PHP_EOL;
        for ($i = 0; $i < count($this->params); $i++) {
            $buffer .= '$'. strlen($this->params[$i]) . PHP_EOL;
            $buffer .= $this->params[$i] . PHP_EOL;
        }

        $this->stream = $buffer;
    }
}