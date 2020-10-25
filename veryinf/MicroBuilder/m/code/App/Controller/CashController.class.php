<?php
namespace App\Controller;
use Core\Model\Member;
use Core\Model\Pay;
use Think\Controller;

class CashController extends Controller {
    
    public function deskAction() {
        $mem = new Member();
        $mem->auth();
        $member = C('MEMBER');
        
        $plid = intval(I('get.p'));
        if(empty($plid)) {
            $this->error('请求错误');
        }
        $m = new Pay();
        $log = $m->fetchLog($plid);
        if($log == Pay::STATUS_DISBURSED || $log['uid'] != $member['uid']) {
            $this->error('错误的支付订单');
        }
        Pay::loadSettings();
        $setting = C('PAY');
        $pay = array();
        $pay['weixin'] = $setting[Pay::OPT_WEIXIN];
        if($pay['weixin']['enable']) {
            $wParams = $m->payWeixin($log);
            $this->assign('wParams', $wParams);
        }
        
        $pay['alipay'] = $setting[Pay::OPT_ALIPAY];
        if($pay['alipay']['enable']) {
            $aParams = $m->payAlipay($log);
            $this->assign('aParams', $aParams);
        }
        
        $this->assign('pay', $pay);
        $this->assign('trade', $log);
        $this->display('desk');
    }
    
    public function weixinAction() {
    }
}

