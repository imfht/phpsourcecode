<?php namespace Crazymus\SimpleRedis;

/*
 * This file is part of the SimpleRedis package.
 *
 * (c) Jingzhou Guo <crazymus@foxmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Reader
{
    /**
     * TCP连接句柄
     *
     * @var Resource
     */
    protected $handler;
    public function __construct($handler)
    {
        $this->handler = $handler;
    }

    /**
     * 从服务器读取响应内容
     *
     * @return bool|string
     */
    public function read()
    {
        $header = fgets($this->handler);
        $first = substr($header, 0, 1);
        $result = '';
        switch ($first) {
            case '+':
                $result = $header;
                break;
            case '-':
                $result = $header;
                break;
            case '$':
                $data = $this->readDollerData($header);
                $result = $header . $data;
        }

        return $result;
    }

    /**
     * 读取$开头的响应内容
     *
     * @param $header
     * @return string
     */
    public function readDollerData($header)
    {
        $data = '';
        $len = (int) substr($header, 1);
        if ($len <= 0) {
            return $data;
        }

        while (strlen($data) < $len + 2) {
            $data .= fgets($this->handler);
        }

        return $data;
    }
}
