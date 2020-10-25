<?php

/**
 * Class Nsq
 * nsq消息发布
 *
 * @datetime: 2017/5/5 16:54
 * @author: lihs
 * @copyright: ec
 */

namespace common;

use log\Log;
use tool\Http;
use Yaf\Registry;

class Nsq {
    protected static $Obj;
    private $nsqUrl;

    private function __construct($nsqServer) {
        $this->setServer($nsqServer);
    }

    /**
     * 获取实例
     *
     * @param string $nsqServer
     * @return Nsq
     */
    public static function getInstance($nsqServer = 'default') {
        if (!isset(self::$Obj)) {
            self::$Obj = new self($nsqServer);
        }
        return self::$Obj;
    }

    /**
     * 设置服务
     *
     * @param $nsqServer
     */
    public function setServer($nsqServer) {
        $nsqConfig = Registry::get('config')->toArray();
        $this->nsqUrl = isset($nsqConfig['nsq'][$nsqServer]) ? $nsqConfig['nsq'][$nsqServer] : '';
    }


    /**
     * 推送一条消息
     *
     * @param $topic
     * @param $data
     * @return bool|string
     */
    public function pub($topic, $data) {
        if (!$topic || !is_string($topic) || !$data) {
            return false;
        }
        if (is_array($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        if (!is_string($data)) {
            return false;
        }
        $url = $this->nsqUrl . '/pub?topic=' . $topic;
        try {
            return Http::post($url, $data);
        } catch (\Exception $E) {
            Log::error('pub error:' . $E->getMessage(), [], 'nsq');
            return false;
        }
    }

    /**
     * 推送多条消息
     *
     * @param $topic
     * @param array $data
     * @return bool|string
     */
    public function mPub($topic, array $data) {
        if (!$topic || !is_string($topic) || !$data) {
            return false;
        }
        $tmp = '';
        foreach ($data as $datum) {
            $tmp .= "\n" . is_array($datum) ? json_encode($datum, JSON_UNESCAPED_UNICODE) : (string)$datum;
        }
        $url = $this->nsqUrl . '/mpub?topic=' . $topic;
        try {
            return Http::post($url, $data);
        } catch (\Exception $E) {
            Log::error('mpub error:' . $E->getMessage(), [], 'nsq');
            return false;
        }
    }
}