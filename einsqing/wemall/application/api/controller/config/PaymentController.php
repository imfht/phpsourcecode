<?php
namespace app\api\controller\config;
use app\api\controller\BaseController;

class PaymentController extends BaseController
{
	//获取支付方式
	public function index(){
		$paymentlist = model('Payment')->where('status',1)->select()->hidden(['config'])->toArray();

		$data['payment'] = $paymentlist;
		return json(['data' => $data, 'msg' => '支付方式', 'code' => 1]);
	}
}