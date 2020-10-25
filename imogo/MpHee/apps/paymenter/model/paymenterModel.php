<?php
class paymenterModel extends baseModel{
	protected $table = 'ppacount_paymenter'; //设置表名
	
	public function paymenteraddlist() 
    {
		return array(
			array('paytype'=>'wepay','payname'=>'微信支付','description'=>'微信支付是由腾讯公司知名移动社交通讯软件微信及第三方支付平台财付通联合推出的移动支付创新产品，旨在为广大微信用户及商户提供更优质的支付服务，微信的支付和安全系统由腾讯财付通提供支持。财付通是持有互联网支付牌照并具备完备的安全体系的第三方支付平台。[<a class="red" href="http://mp.weixin.qq.com" target="_target">点击申请</a>]'),
			array('paytype'=>'alipaymobile','payname'=>'支付宝手机网站支付','description'=>'手机网站支付主要应用于手机、掌上电脑等无线设备的网页上，通过网页跳转或浏览器自带的支付宝快捷支付实现买家付款的功能，资金即时到账。[<a class="red" href="https://b.alipay.com/order/productDetail.htm?productId=2014110308142133" target="_target">点击申请</a>]'),
			array('paytype'=>'balance','payname'=>'余额支付','description'=>'余额是客户在您网站上的虚拟资金帐户。'),
			array('paytype'=>'receivedpay','payname'=>'货到付款','description'=>'客户收到商品时，再进行付款，让客户更放心。'),
		);
    }
	
	public function paymenterlist($condition) 
    {
        return $this->model->table('ppacount_paymenter')->where($condition)->order("sort desc")->select();
    }
	
	public function paymenterinfo($condition)
    {
        return $this->model->table('ppacount_paymenter')->where($condition)->find();
    }
	
	public function paymenteradd($data) 
    {
    	return $this->model->table('ppacount_paymenter')->data($data)->insert();
    }
	
	public function paymenterdelete($condition) 
    {
		return $this->model->table('ppacount_paymenter')->where($condition)->delete();
    }
	
	public function paymenterupdate($condition,$data) 
    {
        return $this->model->table('ppacount_paymenter')->data($data)->where($condition)->update();
    }
	
}