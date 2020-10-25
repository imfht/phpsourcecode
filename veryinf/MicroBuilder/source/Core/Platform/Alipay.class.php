<?php
/**
 * 支付宝服务窗平台
 */
namespace Core\Platform;
use Core\Model\Utility;
use Core\Platform\Alipay\AliClient;
use Think\Log;
use Think\Model;

class Alipay extends Platform {
    /**
     * @var \Core\Model\Account
     */
    private $platform;
    /**
     * @var AliClient;
     */
    private $client;
    /**
     * @var array
     */
    private $params;

    /**
     * @var array
     */
    private  $message;

    /**
     * 特定公众号平台的操作对象构造方法
     *
     * @param array $platform 公号平台基础对象
     */
    public function __construct($platform) {
        $this->platform = $platform;
        $this->client = new AliClient($platform);
        $this->params = I('post.', '', '');
    }

    public function getAccount() {
        return $this->platform;
    }

    public function checkSign() {
        $ret = $this->client->checkSignAndDecrypt($this->params, true, false);
        if(empty($ret)) {
            exit('signature failed');
        }
    }

    public function touchCheck() {
        $pub = Utility::sslTrimKey($this->platform['public_key']);
        $ret = "<biz_content>{$pub}</biz_content><success>true</success>";
        $dat = $this->client->encryptAndSign($ret, false, true);
        parent::touchCheck();
        $message = $this->parse($this->params['biz_content']);
        $rec = array();
        $rec['appid'] = $message['to'];
        $m = new Model();
        $m->table('__PLATFORM_ALIPAY__')->data($rec)->where("`id`='{$this->platform['id']}'")->save();
        exit($dat);
    }

    public function booking($message) {
        $fan = array();
        $fan['openid'] = $message['from'];
        $fan['unionid'] = $message['from'];
        $fan['subscribe'] = 1;
        if($message['type'] == 'subscribe') {
            $fan['subscribetime'] = $message['time'];
        } elseif($message['type'] == 'unsubscribe') {
            $fan['subscribe'] = 0;
            $fan['unsubscribetime'] = $message['time'];
        } else {
            $fan['subscribetime'] = TIMESTAMP;
        }
        $tag = @json_decode($message['original']['userinfo'], true);
        if(!empty($tag)) {
            $fan['tag'] = serialize($tag);
        }
        parent::booking($fan);
    }

    public function parse($message) {
        $msg = array();
        if (!empty($message)){
            $xml = $message;
            $dom = new \DOMDocument();
            if($dom->loadXML($xml)) {
                $xpath = new \DOMXpath($dom);
                $msg['from'] = $xpath->evaluate('string(//XML/FromUserId)');
                $msg['to'] = $xpath->evaluate('string(//XML/AppId)');
                $msg['time'] = $xpath->evaluate('string(//XML/CreateTime)');
                $msg['type'] = 'unknow';
                $elms = $xpath->query('//XML/*');
                foreach($elms as $elm) {
                    if($elm->childNodes->length == 1) {
                        $msg['original'][strtolower($elm->nodeName)] = strval($elm->nodeValue);
                    }
                }
                $type = $xpath->evaluate('string(//XML/MsgType)');
                if($type == 'text') {
                    $msg['type'] = Platform::MSG_TEXT;
                    $msg['content'] = $xpath->evaluate('string(//XML/Text/Content)');
                }
                if($type == 'image') {
                    $msg['type'] = Platform::MSG_IMAGE;
                    $id = $xpath->evaluate('string(//XML/Image/MediaId)');
                    $format = $xpath->evaluate('string(//XML/Image/Format)');
                    $mediaData = $this->client->download($id);
                    $fname = util_random(32) . '.' . $format;
                    file_put_contents(MB_ROOT . 'attachment/media/alipay/' . $fname, $mediaData);
                    $msg['url'] = '/attachment/media/alipay/' . $fname;
                }

                if($type == 'event') {
                    //处理其他事件类型
                    $event = $xpath->evaluate('string(//XML/EventType)');
                    if($event == 'follow') {
                        //开始关注
                        $msg['type'] = Platform::MSG_SUBSCRIBE;
                    }
                    if($event == 'unfollow') {
                        //取消关注
                        $msg['type'] = Platform::MSG_UNSUBSCRIBE;
                    }
                    if($event == 'enter') {
                        //进入对话
                        $msg['type'] = Platform::MSG_ENTER;
                        $scene = @json_decode($message['original']['actionparam'], true);
                        if(!empty($scene)) {
                            $msg['scene'] = $scene['sceneId'];
                        }
                    }
                    if($event == 'click') {
                        $msg['type'] = Platform::MSG_MENU_CLICK;
                        $params = $message['original']['actionparam'];
                        if(!empty($params)) {
                            $msg['params'] = $params;
                        }
                    }
                }
            }
        }
        $this->message = $msg;
        return $msg;
    }

    public function queryAvailablePackets($type = '') {
        return array(
            Platform::POCKET_TEXT,
            Platform::POCKET_NEWS
        );
    }

    public function response($packet) {
        $this->openPush($this->message['from'], $packet);
        return '';
    }


    public function isPushSupported() {
        return true;
    }

    private function openPush($openid, $packet) {
        import_third('aop.request.AlipayMobilePublicMessageCustomSendRequest');
        $request = new \AlipayMobilePublicMessageCustomSendRequest();
        $set = array();
        $set['toUserId'] = $openid;
        $set['createTime'] = TIMESTAMP * 1000;
        if($packet['type'] == Platform::POCKET_TEXT) {
            $packet['content'] = str_replace('微信', 'WeChat', $packet['content']);
            $set['msgType'] = 'text';
            $set['text'] = array();
            $set['text']['content'] = $packet['content'];
            
            $request->setBizContent(json_encode($set));
            $resp = $this->client->execute($request);
            if($resp->alipay_mobile_public_message_custom_send_response->code != 200) {
                Log::write($resp->alipay_mobile_public_message_custom_send_response->msg, Log::WARN);
            }
        }
        if($packet['type'] == Platform::POCKET_NEWS) {
            $set['msgType'] = 'image-text';
            $total = count($packet['news']);
            $times = ceil($total / 4);
            for($i = 0; $i < $times; $i++) {
                $news = array_slice($packet['news'], $i * 4, 4);
                $set['articles'] = array();
                foreach($news as $row) {
                    $set['articles'][] = array(
                        "title" => $row['title'],
                        "desc" => $row['description'],
                        "imageUrl" => $row['picurl'],
                        "actionName" => "查看详情",
                        "url" => $row['url'],
                        "authType" =>"loginAuth"
                    );
                }

                $request->setBizContent(json_encode($set));
                $resp = $this->client->execute($request);
                if($resp->alipay_mobile_public_message_custom_send_response->code != 200) {
                    Log::write($resp->alipay_mobile_public_message_custom_send_response->msg, Log::WARN);
                }
            }
        }
    }
    
    public function push($uid, $packet) {
        $openid = 'L6OOPFU1auUKydq9vHxkKTvoMnZQkHvGW828bvjfD40ZcoV6valQ9EUNUhwK9mTS01';
        $resp = $this->openPush($openid, $packet);
        return true;
    }
}
