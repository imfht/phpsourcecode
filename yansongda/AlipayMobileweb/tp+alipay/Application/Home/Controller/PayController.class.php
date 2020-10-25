<?php
namespace Home\Controller;
use Think\Controller;

/**
* 支付控制器
*/
class PayController extends Controller
{
	/**
	 * 跳转到支付宝付款方法【生成Token -> 用Token获取到跳转链接】
	 * @return [type] [description]
	 */
	public function index()
    {
        $out_trade_no = $_POST['WIDout_trade_no'];//订单系统中唯一订单号，必填
        $subject = $_POST['WIDsubject'];//必填.订单名称
        $total_fee = $_POST['WIDtotal_fee'];//必填.付款金额
        $req_data = '<direct_trade_create_req><notify_url>' . C('notify_url') . '</notify_url><call_back_url>' . C('call_back_url') . '</call_back_url><seller_account_name>' . C('seller_email') . '</seller_account_name><out_trade_no>' . $out_trade_no . '</out_trade_no><subject>' . $subject . '</subject><total_fee>' . $total_fee . '</total_fee><merchant_url>' . C('merchant_url') . '</merchant_url></direct_trade_create_req>';
        $para_token = array(
            "service" => "alipay.wap.trade.create.direct",
            "partner" => trim(C("alipay_config.partner")),
            "sec_id" => trim(C("alipay_config.sign_type")),
            "format" => C('format'),
            "v" => C('v'),
            "req_id" => C('req_id'),
            "req_data"  => $req_data,
            "_input_charset"  => trim(strtolower(C("alipay_config.input_charset")))
        );

        //建立请求获取token
        $getToken = new \Common\Lib\Alipay\AlipayMobileweb(C("alipay_config"));
        $request_token = $getToken->getToken($para_token);

        //业务详细
        $req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';

        //构造要请求的参数数组
        $parameter = array(
            "service" => "alipay.wap.auth.authAndExecute",
            "partner" => trim(C("alipay_config.partner")),
            "sec_id" => trim(C("alipay_config.sign_type")),
            "format" => C('format'),
            "v" => C('v'),
            "req_id" => C('req_id'),
            "req_data" => $req_data,
            "_input_charset" => trim(strtolower(C("alipay_config.input_charset")))
        );
        //获取请求到支付宝的url
        $alipaySubmit = new \Common\Lib\Alipay\AlipayMobileweb(C('alipay_config'));
        $url = $alipaySubmit->url($parameter);
        redirect($url);
    }

    public function return_url()
    {
    	if ( !IS_GET ) {
    		$this->error('请求非法！');
    	}
    	$getRequest = I('get.');
    	$alipay_sign = new \Common\Lib\Alipay\AlipayMobileweb(C('alipay_config'));
    	$isSign = $alipay_sign->verifyReturn($getRequest);
    	if ( $isSign ) {
    		//TODO 验证成功后的业务逻辑
    		echo "验证成功";
    	} else {
			//TODO 验证失败后的业务逻辑
			echo "验证失败";
    	}
    }

    public function notify_url()
    {
    	if ( !IS_POST ) {
    		$this->error('请求非法！');
    	}
    	$postRequest = I('post.');
    	$alipay_sign = new \Common\Lib\Alipay\AlipayMobileweb(C('alipay_config'));
    	$isSign = $alipay_sign->verifyNotify($postRequest);
    	if ( $isSign ) {
    		//TODO 验证成功
    		$doc = new DOMDocument();	
			$doc->loadXML(I('notify_data'));
			
			if( ! empty($doc->getElementsByTagName( "notify" )->item(0)->nodeValue) ) {
				//商户订单号
				$out_trade_no = $doc->getElementsByTagName( "out_trade_no" )->item(0)->nodeValue;
				//支付宝交易号
				$trade_no = $doc->getElementsByTagName( "trade_no" )->item(0)->nodeValue;
				//交易状态
				$trade_status = $doc->getElementsByTagName( "trade_status" )->item(0)->nodeValue;
				
				if($trade_status == 'TRADE_FINISHED') {
					//判断该笔订单是否在商户网站中已经做过处理
						//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
						//如果有做过处理，不执行商户的业务程序
							
					//注意：
					//该种交易状态只在两种情况下出现
					//1、开通了普通即时到账，买家付款成功后。
					//2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。
			
					//调试用，写文本函数记录程序运行情况是否正常
					//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
					
					echo "success";		//请不要修改或删除
				}
				else if ($trade_status == 'TRADE_SUCCESS') {
					//判断该笔订单是否在商户网站中已经做过处理
						//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
						//如果有做过处理，不执行商户的业务程序
							
					//注意：
					//该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。
			
					//调试用，写文本函数记录程序运行情况是否正常
					//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
					
					echo "success";		//请不要修改或删除
				}
			}
    	} else {
    		//TODO 验证失败
    	}
    }
}