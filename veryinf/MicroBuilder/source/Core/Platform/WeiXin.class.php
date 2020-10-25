<?php
/**
 * 微信公众平台
 */
namespace Core\Platform;
use Core\Model\Utility;
use Core\Platform\Alipay\AliClient;
use Core\Util\Net;
use Think\Log;
use Think\Model;

class WeiXin extends Platform {
    public static function getAccessToken($appid, $secret) {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        $content = Net::httpGet($url);
        if(is_error($content)) {
            return error(-1, '获取微信公众号授权失败, 请稍后重试！错误详情: ' . $content['message']);
        }
        $token = @json_decode($content, true);
        if(empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['expires_in'])) {
            $errorinfo = substr($content['meta'], strpos($content['meta'], '{'));
            $errorinfo = @json_decode($errorinfo, true);
            return error(-2, '获取微信公众号授权失败, 请稍后重试！ 公众平台返回原始数据为: 错误代码-' . $errorinfo['errcode'] . '，错误信息-' . $errorinfo['errmsg']);
        }
        $record = array();
        $record['token'] = $token['access_token'];
        $record['expire'] = TIMESTAMP + $token['expires_in'];
        return $record;
    }
    
    /**
     * @var \Core\Model\Account
     */
    private $account;
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
        $this->account = $platform;
        $this->client = new AliClient($platform);
        $this->params = I('post.', '', '');
    }

    public function getAccount() {
        return $this->account;
    }

    public function checkSign() {
        $ret = $this->client->checkSignAndDecrypt($this->params, true, false);
        if(empty($ret)) {
            exit('signature failed');
        }
    }

    public function touchCheck() {
        $pub = Utility::sslTrimKey($this->account['public_key']);
        $ret = "<biz_content>{$pub}</biz_content><success>true</success>";
        $dat = $this->client->encryptAndSign($ret, false, true);
        parent::touchCheck();
        $message = $this->parse($this->params['biz_content']);
        $rec = array();
        $rec['appid'] = $message['to'];
        $m = new Model();
        $m->table('__PLATFORM_ALIPAY__')->data($rec)->where("`id`='{$this->account['id']}'")->save();
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

    public function fansQueryInfo($uniid, $isPlatform) {
        if(!$isPlatform) {
            $openid = '';
        } else {
            $openid = $uniid;
        }
        $token = $this->fetchToken();
        if(is_error($token)) {
            return $token;
        }
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$token}&openid={$openid}&lang=zh_CN";
        $response = Net::httpGet($url);
        if(is_error($response)) {
            return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
        }
        $result = @json_decode($response, true);
        if(empty($result)) {
            return error(-2, "接口调用失败, 错误信息: {$response}");
        } elseif (!empty($result['errcode'])) {
            return error(-3, "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']}");
        }
        $ret = array();
        $ret['nickname']        = $result['nickname'];
        $ret['gender']          = $result['sex'];
        $ret['residecity']      = $result['city'];
        $ret['resideprovince']  = $result['province'];
        $ret['avatar']          = $result['headimgurl'];
        if(!empty($ret['avatar'])) {
            $ret['avatar'] = rtrim($ret['avatar'], '0');
            $ret['avatar'] .= '132';
        }
        $ret['original'] = $result;
        return $ret;
    }
    
    public function createShareData() {
        $t = '';
        $ticket = @unserialize($this->account['jsticket']);
        if(is_array($ticket) && !empty($ticket['ticket']) && !empty($ticket['expire']) && $ticket['expire'] > TIMESTAMP) {
            $t = $ticket['ticket'];
        } else {
            $token = $this->fetchToken();
            if(is_error($token)) {
                return $token;
            }
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$token}&type=jsapi";
            $resp = Net::httpGet($url);
            if(is_error($resp)) {
                return error(-1, "访问公众平台接口失败, 错误: {$resp['message']}");
            }
            $result = @json_decode($resp, true);
            if(empty($result)) {
                return error(-2, "接口调用失败, 错误信息: {$resp}");
            } elseif (!empty($result['errcode'])) {
                return error(-3, "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']}");
            }
            
            $record = array();
            $record['ticket'] = $result['ticket'];
            $record['expire'] = TIMESTAMP + $result['expires_in'];
            $rec = array();
            $rec['jsticket'] = serialize($record);
            $m = new Model();
            $m->table('__PLATFORM_WEIXIN__')->data($rec)->where("`id`='{$this->account['id']}'")->save();
            $this->account['jsticket'] = $rec['jsticket'];
            $t = $record['ticket'];
        }
        
        $share = array();
        $share['appid'] = $this->account['appid'];
        $share['timestamp'] = TIMESTAMP;
        $share['nonce'] = util_random(32);
        $url = __HOST__ . $_SERVER['REQUEST_URI'];
        
        $string1 = "jsapi_ticket={$t}&noncestr={$share['nonce']}&timestamp={$share['timestamp']}&url={$url}";
        $share['signature'] = sha1($string1);
        return $share;
    }

    private function fetchToken() {
        if(is_array($this->account['access_token']) && !empty($this->account['access_token']) && !empty($this->account['access_expire']) && $this->account['access_expire'] > TIMESTAMP) {
            return $this->account['access_token'];
        } else {
            $token = self::getAccessToken($this->account['appid'], $this->account['secret']);
            if(is_error($token)) {
                return $token;
            }
            $record = array();
            $record['access_token'] = $token['token'];
            $record['access_expire'] = TIMESTAMP + $token['expire'];
            $m = new Model();
            $m->table('__PLATFORM_WEIXIN__')->data($record)->where("`id`='{$this->account['id']}'")->save();
            $this->account['access_token'] = $record['access_token'];
            $this->account['access_expire'] = $record['access_expire'];
            return $this->account['access_token'];
        }
    }
}
