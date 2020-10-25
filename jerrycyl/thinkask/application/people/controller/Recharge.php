<?php
/**
 * 
 */
namespace app\people\controller;
use app\common\controller\Base;
use app\common\model\UserFocus;
class Recharge extends Base
{
  public function index(){
    $result = model('pay')->alipay([
        'notify_url' => request()->domain().url('index/index/alipay_notify'),
        'return_url' => '',
        'out_trade_no' => time(),
        'subject' => "test",
        'total_fee' => "100",//订单金额，单位为元
        'body' => "100",
      ]);
    // show(model('pay'));
    // die;
    show($result);
      if(!$result['code']){
        return $this->error($result['msg']);
      }
    // show(model('pay'));
    return $this->fetch('people/recharge/index');
  }
  
  

	
}