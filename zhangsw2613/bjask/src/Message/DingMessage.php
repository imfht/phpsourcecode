<?php
/**
 * 钉钉消息
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/3/28
 * Time: 14:10
 */

namespace Bjask\Message;


use Bjask\Logger;

class DingMessage
{
    private static $config = [];
    private static $logger = null;
    public static $instance = null;

    public static function init(Logger $logger, array $config)
    {
        if (!is_object(self::$instance) || !is_object(self::$logger)) {
            self::$config = $config;
            self::$logger = $logger;
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function send($messages = [])
    {
        $result = [];
        $client = new \GuzzleHttp\Client();
        foreach ($messages as $message) {
            $ret = $client->request('POST', self::$config['url'], [
                'headers' => [
                    'Accept' => 'application/json'
                ],
                'json' => ['msgtype' => 'text', 'text' => array('content' => $message)],
                'timeout' => 5
            ]);
            $result[] = ['code' => $ret->getStatusCode(), 'body' => $ret->getBody()];
        }
        return $result;
    }
}