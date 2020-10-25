<?php
namespace Addon\Bridge\Api;

use Addon\Bridge\Model\Bridge;
use Core\Platform\Platform;
use Core\Util\Net;

class Executor {
    public function exec($message, $processor) {
        if(!empty($processor)) {
            $b = new Bridge(null);
            $platform = $b->getOne($processor['id'], true);
            $body = $this->toRequestXml($message);
            $url = $platform['url'];
                
            if(!strpos($url, '?') == -1) {
                $url .= '?';
            } else {
                $url .= '&';
            }

            $params = array(
                'timestamp' => TIMESTAMP,
                'nonce' => util_random(10, 1),
            );
            $signParams = array($platform['token'], $params['timestamp'], $params['nonce']);
            sort($signParams, SORT_STRING);
            $params['signature'] = sha1(implode($signParams));
            $url .= http_build_query($params, '', '&');
            $ret = Net::httpPost($url, $body);
            if(!empty($ret)) {
                return $this->toPacket($ret);
            }
        }
    }
    
    private function toRequestXml($message) {
        if($message['type'] == Platform::MSG_TEXT) {
            $now = TIMESTAMP;
            $dat = <<<DOT
<xml>
  <ToUserName><![CDATA[{$message['to']}]]></ToUserName>
  <FromUserName><![CDATA[{$message['from']}]]></FromUserName>
  <CreateTime>{$message['time']}</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA[{$message['content']}]]></Content>
  <MsgId>{$now}</MsgId>
</xml>
DOT;
        }
        return $dat;
    }
    
    private function toPacket($xml) {
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . $xml;
        $packet = array();
        $dom = new \DOMDocument();
        if($dom->loadXML($xml)) {
            $xpath = new \DOMXpath($dom);
            $type = $xpath->evaluate('string(//xml/MsgType)');
            if($type == 'text') {
                $packet['type'] = Platform::POCKET_TEXT;
                $packet['content'] = $xpath->evaluate('string(//xml/Content)');
            }
            if($type == 'news') {
                $packet['type'] = Platform::POCKET_NEWS;
                $packet['news'] = array();
                $items = $xpath->query('//xml/Articles/item');
                foreach($items as $item) {
                    $row = array();
                    $row['title'] = $xpath->evaluate('string(Title)', $item);
                    $row['description'] = $xpath->evaluate('string(Description)', $item);
                    $row['picurl'] = $xpath->evaluate('string(PicUrl)', $item);
                    $row['url'] = $xpath->evaluate('string(Url)', $item);
                    $packet['news'][] = $row;
                }
            }
        }
        return $packet;
    }
}

