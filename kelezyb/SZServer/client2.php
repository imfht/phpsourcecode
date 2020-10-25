<?php
use Framework\SZLogger;

define('REQUEST_SIZE', 2);

/**
 * SZServer的客户端
 *
 * @author kelezyb
 * @version 0.9
 * 依赖: swoole和msgpack扩展
 */
class SZClient {
    /**
     * @var swoole_client
     */
    private $client;

    /**
     * @param string $host
     * @param int $port
     */
    public function __construct($host, $port) {
        $this->client = new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);

        if (!$this->client->connect($host, $port, 0.5, 0)) {
            trigger_error($this->client->errCode);
        }
    }


    /**
     * @param string $controller
     * @param string $action
     * @param mixed $params
     * @return mixed
     */
    public function callController($controller, $action, $params) {
        $data = array(
            time(),
            $controller . '.' . $action,
            $params
        );

        return $this->send_pack($data);
    }

    public function __destruct() {
        $this->client->close();
    }

    /**
     * 发送数据包给服务端
     * @param mixed $data
     * @return mixed
     */
    private function send_pack($data) {
        $data = msgpack_pack($data);
        $len = strlen($data);
        $buffer = '';
        $buffer .= pack('N', $len);
        $buffer .= $data;

        if (!$this->client->send($buffer)) {
            SZLogger::error("Send failed. - " . var_export($data, true));
        }

        $header = $this->client->recv(4);
        list(, $len) = unpack('N', $header);
        $body = $this->client->recv($len);
        $datas = msgpack_unpack($body);

        return $datas;
    }
}


$start = microtime(true);
for ($i = 0; $i < REQUEST_SIZE; $i++) {//
    $host = '127.0.0.1';
    $port = 9502;
    $client = new SZClient($host, $port);
    print_r($client->callController('Index', 'Index', array(
            'a' => '2',
            'b' => 'test',
            'c' => microtime(true)
        )));

//    print_r($client->callController('User', 'Login', array('uid' => 123456/*mt_rand(100000, 200000)*/)));
}

$end = microtime(true);
echo sprintf("%0.4f ms\n", ($end - $start) * 1000 / REQUEST_SIZE);