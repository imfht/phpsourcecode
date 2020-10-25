<?php
namespace Wander\Controller;
use Core\Model\Pay;
use Think\Controller;

class PaymentController extends Controller {
    
    public function alipayAction() {
        $t = I('get.t');
        $get = I('get.');
        unset($get['t']);
        
        Pay::loadSettings();
        $setting = C('PAY');
        $pay = $setting[Pay::OPT_ALIPAY];
        
        $p = new Pay();
        if($t == 'return') {
            $plid = $get['out_trade_no'];
            if(empty($plid)) {
                $this->error('非法访问');
            }
            
            $prepares = array();
            foreach($get as $key => $value) {
                if($key != 'sign' && $key != 'sign_type') {
                    $prepares[] = "{$key}={$value}";
                }
            }
            sort($prepares);
            $string = implode($prepares, '&');
            $string .= $pay['secret'];
            $sign = md5($string);
            if($sign == $get['sign'] && $get['result'] == 'success') {
                $log = $p->fetchLog($plid);
                if(!empty($log)) {
                    dump($log);
                    exit;

                    $site = WeUtility::createModuleSite($log['module']);
                    if(!is_error($site)) {
                        $method = 'payResult';
                        if (method_exists($site, $method)) {
                            $ret = array();
                            $ret['weid'] = $log['weid'];
                            $ret['uniacid'] = $log['uniacid'];
                            $ret['result'] = $log['status'] == '1' ? 'success' : 'failed';
                            $ret['type'] = $log['type'];
                            $ret['from'] = 'return';
                            $ret['tid'] = $log['tid'];
                            $ret['user'] = $log['openid'];
                            $ret['fee'] = $log['fee'];
                            exit($site->$method($ret));
                        }
                    }
                }
            }

        }
        if($t == 'notify') {
            $xml = '<?xml version="1.0" encoding="utf-8"?>' . I('post.notify_data');
            $dom = new \DOMDocument();
            if($dom->loadXML($xml)) {
                $xpath = new \DOMXPath($dom);
                $plid = $xpath->evaluate('string(//notify/out_trade_no)');
                $post = I('post.');
                
                $string = "service={$post['service']}&v={$post['v']}&sec_id={$post['sec_id']}&notify_data={$post['notify_data']}";
                $string .= $pay['secret'];
                $sign = md5($string);
                if($sign == $post['sign']) {
                    $log = $p->fetchLog($plid);
                    if(!empty($log) && $log['status'] == Pay::STATUS_CREATED) {
                        $record = array();
                        $record['status'] = Pay::STATUS_DISBURSED;
                        pdo_update('core_paylog', $record, array('plid' => $log['plid']));

                        $site = WeUtility::createModuleSite($log['module']);
                        if(!is_error($site)) {
                            $method = 'payResult';
                            if (method_exists($site, $method)) {
                                $ret = array();
                                $ret['weid'] = $log['weid'];
                                $ret['uniacid'] = $log['uniacid'];
                                $ret['result'] = 'success';
                                $ret['type'] = $log['type'];
                                $ret['from'] = 'notify';
                                $ret['tid'] = $log['tid'];
                                $ret['user'] = $log['openid'];
                                $ret['fee'] = $log['fee'];
                                $site->$method($ret);
                                exit('success');
                            }
                        }
                    }
                }
            }
        }
        if($t == 'merchant') {
            
        }
    }
}