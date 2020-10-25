<?php

namespace App\Http\Controllers;

class AriaController
{
    /**
     * Fork with https://github.com/Yuav/php-aria2.
     */
    private $server;
    private $ch;

    public function __construct()
    {
        $this->server = env('SERVER_ARIA', 'http://localhost:6800/jsonrpc');
        $this->ch = curl_init($this->server);
        curl_setopt_array($this->ch, array(
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_CONNECTTIMEOUT => 5,
        ));
    }

    public function __destruct()
    {
        curl_close($this->ch);
    }

    private function request($data)
    {
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);

        return curl_exec($this->ch);
    }

    public function __call($name, $arg)
    {
        $data = array(
            'jsonrpc' => '2.0',
            'id' => '1',
            'method' => 'aria2.'.$name,
            'params' => $arg,
        );
        $data = json_encode($data);

        return json_decode($this->request($data), 1);
    }
}
