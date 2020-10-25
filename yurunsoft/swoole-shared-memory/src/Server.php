<?php
namespace Yurun\Swoole\SharedMemory;

use Yurun\Swoole\SharedMemory\Interfaces\IServer;
use Swoole\Coroutine;
use Yurun\Swoole\SharedMemory\Message\Operation;
use Yurun\Swoole\SharedMemory\Message\Result;
use Yurun\Swoole\SharedMemory\OperationParser;

class Server implements IServer
{
    /**
     * 配置
     *
     * @var array
     */
    private $options = [];

    /**
     * socket 文件路径
     * 
     * 不支持 samba 文件共享
     *
     * @var string
     */
    private $socketFile;

    /**
     * 序列化方法
     *
     * @var callable
     */
    private $serialize;

    /**
     * 反序列化方法
     *
     * @var callable
     */
    private $unserialize;

    /**
     * 存储类型
     *
     * @var array
     */
    private $storeTypes;

    /**
     * socket 资源
     *
     * @var resource
     */
    private $socket;

    /**
     * 操作处理器
     *
     * @var \Yurun\Swoole\SharedMemory\OperationParser
     */
    private $operationParser;

    /**
     * 构造方法
     *
     * @param array $options 监听配置
     */
    public function __construct($options = [])
    {
        if(!('cli' === php_sapi_name() && extension_loaded('swoole')))
        {
            throw new \RuntimeException('Must run with cli and swoole');
        }
        $this->options = $options;
        if(!isset($this->options['socketFile']))
        {
            throw new \InvalidArgumentException('If you want to use Swoole Shared Memory, you must set the "socketFile" option');
        }
        $this->socketFile = $this->options['socketFile'];
        $this->serialize = $this->options['serialize'] ?? 'serialize';
        $this->unserialize = $this->options['unserialize'] ?? 'unserialize';
        $this->storeTypes = $this->options['storeTypes'];
        $this->operationParser = new OperationParser($this->storeTypes);
    }

    /**
     * 运行服务器
     *
     * @return void
     */
    public function run()
    {
        if(version_compare(SWOOLE_VERSION, '4.1', '>='))
        {
            \Swoole\Runtime::enableCoroutine(true);
        }
        if(file_exists($this->socketFile))
        {
            unlink($this->socketFile);
        }
        go(function(){
            $this->socket = stream_socket_server('unix://' . $this->socketFile, $errno, $errstr);
            if(false === $this->socket)
            {
                throw new \RuntimeException(sprintf('Create unix socket server failed, errno: %s, errstr: %s', $errno, $errstr));
            }
            while(true)
            {
                $conn = stream_socket_accept($this->socket, 0);
                if(false === $conn)
                {
                    continue;
                }
                go(function() use($conn){
                    $this->parseConn($conn);
                });
            }
        });
    }

    /**
     * 获取配置
     *
     * @return void
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * 设置配置
     *
     * @param array $options
     * @return void
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * 处理连接
     *
     * @param resource $conn
     * @return void
     */
    private function parseConn($conn)
    {
        while(true)
        {
            $meta = fread($conn, 4);
            if('' === $meta || false === $meta)
            {
                return;
            }
            $length = unpack('N', $meta)[1];
            $data = fread($conn, $length);
            if(false === $data || !isset($data[$length - 1]))
            {
                return;
            }
            $body = ($this->unserialize)($data);
            if(!$body instanceof Operation)
            {
                return;
            }
            try{
                $result = new Result($this->operationParser->parse($body));
            } catch(\Throwable $th) {
                $result = new Result(null, $th);
            }
            $resultData = ($this->serialize)($result);
            $length = strlen($resultData);
            $resultData = pack('N', $length) . $resultData;
            fwrite($conn, $resultData, $length + 4);
        }
    }
}