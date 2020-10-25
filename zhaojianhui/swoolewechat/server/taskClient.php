<?php
class QueueClient
{
    private $client;
    private $isConnect = false;
    public function __construct()
    {
        $this->client = new swoole_client(SWOOLE_SOCK_TCP);

    }
    public function connect()
    {
        if ($this->client->connect('127.0.0.1', 9443, 1)){
            $this->isConnect = true;
        }
    }
    public function send($data)
    {
        $data = [
            'queueName' => 'Home/time',
            'recData' => [
                'name' => '赵建辉',
                'age' => 28,
                'data' => $data
            ],
        ];
        $jsonData = json_encode($data);
        if ($this->isConnect){
            $this->client->send($jsonData);
        }
    }
    public function close()
    {
        if ($this->isConnect){
            $this->client->close();
        }
    }
}
$client = new QueueClient();
$client->connect();
for ($i=0;$i<1000;$i++){
    $client->send($i);
    usleep(1000);
}
$client->close();
