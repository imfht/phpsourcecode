<?php
// 本文档自动生成，仅供测试运行
class paymentAction extends frontendAction
{
	//private $return_page = U('/ucenter/tongji');
	public function _initialize() {
		parent::_initialize(false);
	}
	protected function _getClass($mark){
		$payment = D('Payment');
		$paymentmap = array(
			'mark'=>array('eq',$mark),
			'status'=>array('eq',1),
		);
		$paymentdata = $payment->where($paymentmap)->find();
		if(empty($paymentdata)){
			return false;
		}else{
			$import_status = import("@.ORG.{$paymentdata['mark']}");
			if($import_status){
				$pay = new $paymentdata['mark']($paymentdata);
				return $pay;
			}else{
				return false;
			}
		}
	}

	protected function _returnVerify($pay){
		$this->assign("jumpUrl",U('ucenter/tongji'));
		if($pay){
			$info = $pay->_return_url();
			
			
			
			
			if ($info){
				//成功执行需要操作的方法
				$this->succ($info);
				$this->success(L('pay_success'));
			}else{
				$this->error(L('pay_error'));
			}
		}else{
			$this->error(L('pay_error'));
		}
	}

	protected function _notifyVerify($pay){
		if($pay){
			$info = $pay->_notify_url();
			if ($info){
				//成功执行需要操作的方法
				$this->succ($info);
			}else{
				//失败方法
			}
		}
	}

	# 微信加载
	private function wechat()
	{
		import("@.ORG.payment.wechat.Pay");
    	$payment = D('Payment');
		$paymentmap = array(
			'mark'=>array('eq','Wechat'),
			'status'=>array('eq',1),
		);
    	$config = $payment->where($paymentmap)->find();
        $pay = new Org\Payment\Wechat\Pay($config);
        return $pay;
	}

	# 微信通知接口
	public function wechat_notify()
	{
		$pay = $this->wechat();
		$pay->notify();
	}

	# 微信内下单
	public function wechat_jsapi()
    {
        global $userinfo;
        $num = $this->_request('num','trim');
	    $id = $this->_request('id','trim');
        if ($userinfo && isset($userinfo['uid']) && $userinfo['uid'] > 0) {
        	$name = $userinfo['username'] . '充值';
	        if ($num > 0 && $id > 0) {
	        	$num = $num * 100;
	        	$pay = $this->wechat();
		        $openid = $pay->getOpenid();
		        $recharge = D('recharge');
		        $orderid = $recharge->produceSn();
		        $cash = $num / C('wkcms_score_pay.getscore');
		        $rechargedata = array('sn' => $orderid, 'uid' => $userinfo['uid'], 'uname' => getusername($userinfo['uid']), 'score' => $num, 'cash' => $cash, 'bank_id' => '微信', 'add_time' => time(),);
		        $rid = $recharge->add($rechargedata);

		        if ($rid) {
		        	$url = U('doc/doccon', array('id' => $id));
	        		$result = $pay->jsapi($url, $orderid, $openid, $name, $cash);

	        		echo $result;die;
		        }
	        }
        } else {
        	$param['refer'] = base64_encode(U('payment/wechat_jsapi', array('num' => $num, 'id' => $id)));
	        if ($this->is_weixin()) {
	            $param['mod'] = 'wechat';
	            $param['type'] = 'login';
	            $this->redirect('oauth/index', $param);
	        } else {
	        	$this->redirect('user/login', $param);
	        }
        }
        
        return false;
    }

    public function alipay_double_notify_url()
    {
        $pay = $this->_getClass('Alipay_double');
        $this->_notifyVerify($pay);
    }

    public function alipay_double_return_url()
    {
        $pay = $this->_getClass('Alipay_double');
        $this->_returnVerify($pay);
    }

    public function alipay_notify_url()
    {
    	$pay = $this->_getClass('Alipay');
		$this->_notifyVerify($pay);
    }
    
 	public function alipay_return_url()
    {
    	$pay = $this->_getClass('Alipay');
		$this->_returnVerify($pay);
    }

	public function yeepay_return_url()
    {
		$pay = $this->_getClass('Yeepay');
		$this->_returnVerify($pay);
    }
    
	public function chinabank_return_url()
    {
		$pay = $this->_getClass('Chinabank');
		$this->_returnVerify($pay);
    }
    
	public function tenpay_return_url()
    {
		$pay = $this->_getClass('Tenpay');
		$this->_returnVerify($pay);
    }

	public function chinapnr_notify_url()
    {
		$pay = $this->_getClass('Chinapnr');
    	$this->_notifyVerify($pay);
    }
    
 	public function chinapnr_return_url()
    {
		$pay = $this->_getClass('Chinapnr');
		$this->_returnVerify($pay);
    }
    
	protected function succ($info){
		$pre = substr($info['sn'],0,2);
		if($pre == 'PA'){
    		$order = D('order');
    		$order->succPay($info);
		}elseif ($pre == 'RE'){
			$recharge = D('recharge');
    		$recharge->succPay($info);
		}
    }
}
?>