<?php namespace Crazymus\SimpleRedis;

/*
 * This file is part of the SimpleRedis package.
 *
 * (c) Jingzhou Guo <crazymus@foxmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Crazymus\SimpleRedis\Exception\SimpleRedisException;

class Connection
{
    /**
     * @var Connection
     */
    protected $instance;

    /**
     * @var false|resource redis连接
     */
    protected $handler;

    /**
     * @var Writer
     */
    protected $writer;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var string 服务器地址
     */
    protected $host;

    /**
     * @var string 端口
     */
    protected $port;

    /**
     * @var string 密码
     */
    protected $password;

    /**
     * @var string 超时时间
     */
    protected $timeout = 5;

    /**
     * @var int 连接错误码
     */
    protected $errno;

    /**
     * @var int 连接错误信息
     */
    protected $errmsg;

    public function __construct($config)
    {
        $fields = [ 'host', 'port', 'password', 'timeout' ];
        foreach ($fields as $field) {
            if (isset($config[$field])) {
                $this->{$field} = $config[$field];
            }
        }

        $this->handler = fsockopen($this->host, $this->port, $errno, $errmsg, $this->timeout);
        if (!$this->handler) {
            throw new SimpleRedisException('连接redis失败', ErrorCode::DEFAULT_CODE);
        }

        $this->writer = new Writer($this->handler);
        $this->reader = new Reader($this->handler);

        if ($this->password && !$this->auth($this->password)) {
            throw new SimpleRedisException('密码认证失败', ErrorCode::DEFAULT_CODE);
        }
    }

    /**
     * 验证Redis服务器密码
     *
     * @param $password
     * @return bool
     */
    public function auth($password)
    {
        $params = [
            'Auth',
            $password
        ];
        $command = new Command($params);
        $output = $this->sendCommand($command);
        $response = new Response($output);
        return $response->getStatus();
    }

    /**
     * 发送Redis命令
     *
     * @param Command $command
     * @return bool|string
     */
    public function sendCommand(Command $command)
    {
        $this->writer->send($command->getStream());
        $output = $this->reader->read();
        return $output;
    }
}
