<?php
namespace Core\Model;
use Core\Util\Net;
use Think\Model;
use Think\Upload;

class Pay extends Model {
    protected $autoCheckFields = false;
    /**
     * 新建支付订单
     */
    const STATUS_CREATED = 'created';
    
    /**
     * 支付订单已支付
     */
    const STATUS_DISBURSED = 'disbursed';
    
    const OPT_WEIXIN = 'WEIXIN';
    const OPT_ALIPAY = 'ALIPAY';
    
    private static function getOptions() {
        $keys = array();
        $keys[] = self::OPT_WEIXIN;
        $keys[] = self::OPT_ALIPAY;
        return $keys;
    }
    
    public static function loadSettings($flush = false) {
        $s = C('PAY');
        if(empty($s) || $flush) {
            $keys = self::getOptions();
            $s = Utility::loadSettings('PAY', $keys);
            if(empty($s[self::OPT_WEIXIN])) {
                $s[self::OPT_WEIXIN] = array(
                    'enable' => false
                );
            }
            if(empty($s[self::OPT_ALIPAY])) {
                $s[self::OPT_ALIPAY] = array(
                    'enable' => false
                );
            }
            C('PAY', $s);
        }
    }

    public static function saveSettings($settings) {
        $keys = self::getOptions();
        $settings = coll_elements($keys, $settings);
        return Utility::saveSettings('PAY', $settings);
    }

    /**
     * $ret['url']
     */
    public function payAlipay($log) {
        $gateway = 'http://wappaygw.alipay.com/service/rest.htm?';
        
        self::loadSettings();
        $setting = C('PAY');
        $pay = $setting[self::OPT_ALIPAY];
        $set = array();
        $set['service'] = 'alipay.wap.trade.create.direct';
        $set['format'] = 'xml';
        $set['v'] = '2.0';
        $set['partner'] = $pay['partner'];
        $set['req_id'] = $log['plid'];
        $set['sec_id'] = 'MD5';
        $callback = __HOST__ . U('wander/payment/alipay/t/return');
        $notify = __HOST__ . U('wander/payment/alipay/t/nofity');
        $merchant = __HOST__ . U('wander/payment/alipay/t/merchant');
        $expire = 10;
        $set['req_data'] = "<direct_trade_create_req><subject>{$log['title']}</subject><out_trade_no>{$log['plid']}</out_trade_no><total_fee>{$log['fee']}</total_fee><seller_account_name>{$pay['account']}</seller_account_name><call_back_url>{$callback}</call_back_url><notify_url>{$notify}</notify_url><out_user>{$log['uid']}</out_user><merchant_url>{$merchant}</merchant_url><pay_expire>{$expire}</pay_expire></direct_trade_create_req>";
        $prepares = array();
        foreach($set as $key => $value) {
            if($key != 'sign') {
                $prepares[] = "{$key}={$value}";
            }
        }
        sort($prepares);
        $string = implode($prepares, '&');
        $string .= $pay['secret'];
        $set['sign'] = md5($string);
        $response = Net::httpGet($gateway . http_build_query($set));
        if(is_error($response)) {
            return $response;
        }
        $ret = array();
        @parse_str($response, $ret);
        foreach($ret as &$v) {
            $v = str_replace('\"', '"', $v);
        }
        if(is_array($ret)) {
            if($ret['res_error']) {
                $dom = new \DOMDocument();
                if(!$dom->loadXML($ret['res_error'])) {
                    return error(-1, '支付宝支付初始化失败');
                }
                $xpath = new \DOMXPath($dom);
                return error(-2, '支付宝支付初始化失败, 详细错误: ' . $xpath->evaluate('string(//err/detail)'));
            }

            if($ret['partner'] == $set['partner'] && $ret['req_id'] == $set['req_id'] && $ret['sec_id'] == $set['sec_id'] && $ret['service'] == $set['service'] && $ret['v'] == $set['v']) {
                $prepares = array();
                foreach($ret as $key => $value) {
                    if($key != 'sign') {
                        $prepares[] = "{$key}={$value}";
                    }
                }
                sort($prepares);
                $string = implode($prepares, '&');
                $string .= $pay['secret'];
                if(md5($string) == $ret['sign']) {
                    $dom = new \DOMDocument();
                    if(!$dom->loadXML($ret['res_data'])) {
                        return error(-3, '支付宝支付初始化失败');
                    }
                    $xpath = new \DOMXPath($dom);

                    $token = $xpath->evaluate('string(//direct_trade_create_res/request_token)');
                    $set = array();
                    $set['service'] = 'alipay.wap.auth.authAndExecute';
                    $set['format'] = 'xml';
                    $set['v'] = '2.0';
                    $set['partner'] = $pay['partner'];
                    $set['sec_id'] = 'MD5';
                    $set['req_data'] = "<auth_and_execute_req><request_token>{$token}</request_token></auth_and_execute_req>";
                    $prepares = array();
                    foreach($set as $key => $value) {
                        if($key != 'sign') {
                            $prepares[] = "{$key}={$value}";
                        }
                    }
                    sort($prepares);
                    $string = implode($prepares, '&');
                    $string .= $pay['secret'];
                    $set['sign'] = md5($string);
                    $url = $gateway . http_build_query($set);
                    return array('url' => $url);
                }
            }
        }
        return error(-4, '支付宝支付初始化失败');
    }

    /**
     * @param $log
     * @return js payment object
     */
    public function payWeixin($log) {
        self::loadSettings();
        $setting = C('PAY');
        $pay = $setting[self::OPT_WEIXIN];
        $a = new Account();
        $account = $a->getAccount($pay['account']);
        $pay['appid'] = $account['appid'];
        $pay['secret'] = $account['secret'];

        $wOpt = array();
        $m = new Member();
        $fan = $m->fetchFan($log['uid'], $pay['account']);
        
        $package = array();
        $package['appid'] = $pay['appid'];
        $package['mch_id'] = $pay['mchid'];
        $package['nonce_str'] = util_random(8);
        $package['body'] = $log['title'];
        $package['attach'] = $log['plid'];
        $package['out_trade_no'] = md5($log['plid']);
        $package['total_fee'] = $log['fee'] * 100;
        $package['spbill_create_ip'] = get_client_ip();
        $package['time_start'] = date('YmdHis', TIMESTAMP);
        $package['time_expire'] = date('YmdHis', TIMESTAMP + 600);
        $package['notify_url'] = __HOST__ . U('wander/payment/weixin/t/notify');
        $package['trade_type'] = 'JSAPI';
        $package['openid'] = $fan['openid'];

        ksort($package, SORT_STRING);
        $string1 = '';
        foreach($package as $key => $v) {
            $string1 .= "{$key}={$v}&";
        }
        $string1 .= "key={$pay['key']}";
        $package['sign'] = strtoupper(md5($string1));
        $dat = util_2xml($package);
        $response = Net::httpPost('https://api.mch.weixin.qq.com/pay/unifiedorder', $dat);
        if (is_error($response)) {
            return $response;
        }
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . $response;
        $dom = new \DOMDocument();
        if(!$dom->loadXML($xml)) {
            return error(-1, 'error response');
        }
        $xpath = new \DOMXPath($dom);
        if ($xpath->evaluate("string(//xml/return_code)") == 'FAIL') {
            return error(-2, $xpath->evaluate("string(//xml/return_msg)"));
        }
        if ($xpath->evaluate("string(//xml/result_code)") == 'FAIL') {
            return error(-3, $xpath->evaluate("string(//xml/err_code_des)"));
        }
        $prepayid = $xpath->evaluate("string(//xml/prepay_id)");
        $wOpt['appId'] = $pay['appid'];
        $wOpt['timeStamp'] = TIMESTAMP;
        $wOpt['nonceStr'] = util_random(8);
        $wOpt['package'] = 'prepay_id='.$prepayid;
        $wOpt['signType'] = 'MD5';
        ksort($wOpt, SORT_STRING);
        $string = '';
        foreach($wOpt as $key => $v) {
            $string .= "{$key}={$v}&";
        }
        $string .= "key={$pay['key']}";
        $wOpt['paySign'] = strtoupper(md5($string));
        return $wOpt;
    }
    
    public function fetchLog($plid) {
        $pars = array();
        $pars[':plid'] = $plid;
        $log = $this->table('__CORE_PAYLOGS__')->where('`plid`=:plid')->bind($pars)->find();
        return $log;
    }
    
    public function saveLog($order) {
        if(empty($order['uid']) || empty($order['title']) || empty($order['fee']) || empty($order['tid']) || empty($order['addon'])) {
            return error(-1, 'error arguments');
        }
        $rec = coll_elements(array('uid', 'tid', 'fee', 'title', 'addon'), $order);

        $pars = array();
        $pars[':tid'] = $rec['tid'];
        $pars[':addon'] = $rec['addon'];
        $log = $this->table('__CORE_PAYLOGS__')->where('`tid`=:tid AND `addon`=:addon')->bind($pars)->find();
        if(!empty($log)) {
            if($log['status'] == self::STATUS_DISBURSED) {
                return error(-2, '这个订单已经支付过了');
            }
            $this->table('__CORE_PAYLOGS__')->data($rec)->where("`plid`='{$log['plid']}'")->save();
        } else {
            $rec['type'] = '';
            $rec['status'] = self::STATUS_CREATED;
            $rec['extras'] = '';
            $ret = $this->table('__CORE_PAYLOGS__')->add($rec);
            if(empty($ret)) {
                return error(-3, '创建支付订单失败');
            }
            $log = $rec;
            $log['plid'] = $this->getLastInsID();
        }
        return $log['plid'];
    }
}
