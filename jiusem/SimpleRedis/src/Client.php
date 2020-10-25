<?php namespace Crazymus\SimpleRedis;

/*
 * This file is part of the SimpleRedis package.
 *
 * (c) Jingzhou Guo <crazymus@foxmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Client
{
    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * 读取一个字符串缓存
     *
     * @param $key
     * @return string
     */
    public function get($key)
    {
        $params = [ 'GET', $key ];
        $command = new Command($params);
        $output = $this->connection->sendCommand($command);
        var_dump($output);
        $response = new Response($output);
        return $response->getData();
    }

    /**
     * 设置一条字符串缓存
     *
     * @param $key
     * @param $value
     * @return bool
     */
    public function set($key, $value)
    {
        $params = [ 'SET', $key, $value ];
        $command = new Command($params);
        $output = $this->connection->sendCommand($command);
        $response = new Response($output);
        return $response->getStatus();
    }
}