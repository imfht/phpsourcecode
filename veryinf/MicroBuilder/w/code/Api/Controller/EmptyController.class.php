<?php
/**
 * 支付宝服务窗接口
 */
namespace Api\Controller;
use Core\Model\Account;
use Core\Model\Addon;
use Core\Model\Processor;
use Core\Platform\Platform;
use Think\Controller;
use Think\Log;

class EmptyController extends Controller {
    /**
     * @var Platform
     */
    private $platform;
    private $account;

    public function _empty(){
        //file_put_contents('D:\\1.log', serialize($_POST));
        //$dat = file_get_contents('D:\\1.log');
        //$_POST = unserialize($dat);
        $name = strtolower(CONTROLLER_NAME);
        if($name == 'uc') {
            //接入uc
        }
        $id = intval($name);
        if(empty($id)) {
            exit('request failed. miss platform id');
        }
        $this->platform = Platform::create($id);
        if(empty($this->platform)) {
            exit('request failed. error platform id');
        }

        $this->platform->checkSign();
        $this->account = $this->platform->getAccount();
        if($this->account['type'] == Account::ACCOUNT_ALIPAY) {
            $this->procAlipay();
        }
    }

    private function route($message) {
        // session 启动会话支持
        $sessionid = md5($message['from'] . $message['to']);
        session_id($sessionid);
        session_start();
        Addon::autoload();
        $p = new Processor();
        if($message['type'] == Platform::MSG_TEXT) {
            $packet = $p->procText($message);
        } else {
            $packet = $p->procOther($message);
        }

        //Log::write(var_export($packet, true), Log::INFO);
        $resp = $this->platform->response($packet);
        exit($resp);
    }

    private  function procAlipay() {
        $post = I('post.', '', '');
        if($post['service'] == 'alipay.service.check') {
            $this->platform->touchCheck();
        }
        if($post['service'] == 'alipay.mobile.public.message.notify') {
            $xml = '<?xml version="1.0" encoding="utf-8"?>' . iconv('gbk', 'utf-8', $post['biz_content']);
            $message = $this->platform->parse($xml);
            //Log::write(var_export($message, true), Log::INFO);
            // booking 登记会员
            $this->platform->booking($message);
            $this->route($message);
        }
    }
}

