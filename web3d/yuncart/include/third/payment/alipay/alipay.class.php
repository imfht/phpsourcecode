<?php

//支付宝
class Alipay
{

    private $partner = ''; //支付宝商户号
    private $key = ''; //支付宝密钥
    private $return_url; //显示支付结果页面,
    private $notify_url; //支付完成后的回调处理页面
    private $code = '';
    private $input_charset = 'utf-8';
    private $seller_email = '';
    private $sign_type = 'MD5';
    private $config = array();

    public function __construct($config)
    {
        $this->partner = $config['partner'];
        $this->key = $config['key'];
        $this->return_url = $config['return'];
        $this->notify_url = $config['notify'];
        $this->code = $config['code'];
        $this->input_charset = $config['input_charset'];
        $this->seller_email = $config['seller_email'];
        $this->sign_type = $config['sign_type'];
        $this->config = $config;
    }

    //支付宝发货
    public function send($outtradeid, $company, $sendno)
    {
        if (!$company || !$sendno || !$outtradeid)
            return false;
        //支付宝交易号。它是登陆支付宝网站在交易管理中查询得到，一般以8位日期开头的纯数字（如：20100419XXXXXXXXXX） 
        $trade_no = $outtradeid;

        //物流公司名称
        $logistics_name = $company;

        //物流发货单号
        $invoice_no = $sendno;

        //物流发货时的运输类型，三个值可选：POST（平邮）、EXPRESS（快递）、EMS（EMS）
        $transport_type = "EXPRESS";

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "send_goods_confirm_by_platform",
            "partner" => trim($this->partner),
            "_input_charset" => trim(strtolower($this->input_charset)),
            "trade_no" => $trade_no,
            "logistics_name" => $logistics_name,
            "invoice_no" => $invoice_no,
            "transport_type" => $transport_type
        );
        require(THIRDPATH . '/payment/alipay/alipay_service.class.php');
        //构造确认发货接口
        $alipayService = new AlipayService($this->config);
        $doc = $alipayService->send_goods_confirm_by_platform($parameter);
        if (!$doc)
            return false;

        $nodes = $doc->xpath("is_success");
        $is_success = strval($nodes[0]);
        if ($is_success == "T") {//如果支付宝发货成功
            $nodes = $doc->xpath("response/tradeBase");
            return (array) $nodes[0];
        }
        return false;
    }

    //支付宝请求
    public function request($trade)
    {

        $out_trade_no = $trade['tradeid'];    //请与贵网站订单系统中的唯一订单号匹配
        $subject = $trade['tradeid'];    //订单名称，显示在支付宝的交易管理的“商品名称”的列表里。
        $body = $trade["memo"];         //订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里
        $price = getPrice($trade["totalfee"], -2, "float");   //订单总金额，显示在支付宝收银台里的“应付总额”里

        $logistics_fee = "0.00";    //物流费用，即运费。
        $logistics_type = "EXPRESS";   //物流类型，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
        $logistics_payment = "SELLER_PAY";   //物流支付方式，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）

        $quantity = "1";     //商品数量，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品。
        //选填参数//
        //买家收货信息（推荐作为必填）
        //该功能作用在于买家已经在商户网站的下单流程中填过一次收货信息，而不需要买家在支付宝的付款流程中再次填写收货信息。
        //若要使用该功能，请至少保证receive_name、receive_address有值
        //收货信息格式请严格按照姓名、地址、邮编、电话、手机的格式填写
        $receive_name = $trade["receiver_name"];  //收货人姓名，如：张三
        $receive_address = getDistrict($trade["receiver_province"], $trade["receiver_city"], $trade["receiver_district"]) . $trade["receiver_address"];
        $receive_zip = $trade["receiver_zip"];  //收货人邮编，如：123456
        $receive_phone = $trade["receiver_link"];  //收货人电话号码，如：0571-81234567
        $receive_mobile = $trade["receiver_link"];  //收货人手机号码，如：13312341234
        //网站商品的展示地址，不允许加?id=123这类自定义参数
        $show_url = "";

        /*         * ********************************************************* */

        //构造要请求的参数数组
        $parameter = array(
            "service" => "create_partner_trade_by_buyer",
            "payment_type" => "1",
            "partner" => trim($this->partner),
            "_input_charset" => trim(strtolower($this->input_charset)),
            "seller_email" => trim($this->seller_email),
            "return_url" => trim($this->return_url),
            "notify_url" => trim($this->notify_url),
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "body" => $body,
            "price" => $price,
            "quantity" => $quantity,
            "logistics_fee" => $logistics_fee,
            "logistics_type" => $logistics_type,
            "logistics_payment" => $logistics_payment,
            "receive_name" => $receive_name,
            "receive_address" => $receive_address,
            "receive_zip" => $receive_zip,
            "receive_phone" => $receive_phone,
            "receive_mobile" => $receive_mobile,
            "show_url" => $show_url
        );
        require(THIRDPATH . "/payment/alipay/alipay_service.class.php");
        $alipayService = new AlipayService($this->config);
        $html_text = $alipayService->create_partner_trade_by_buyer($parameter);
        header("Content-type:text/html;charset=utf-8");
        echo $html_text;
    }

    //notify_url
    public function pnotify()
    {
        require_once THIRDPATH . "/payment/alipay/alipay_notify.class.php";
        $alipayNotify = new AlipayNotify($this->config);
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {//验证成功
            //获取支付宝的通知返回参数，
            $out_trade_no = $_POST['out_trade_no'];     //获取订单号
            $trade_no = $_POST['trade_no'];      //获取支付宝交易号
            $total = $_POST['price'];    //获取总价格

            if ($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
                //该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款
                exit("success");
            } else if ($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
                //该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
                PayTrade::setTrade($out_trade_no, array("status" => "WAIT_SEND",
                    "outtradeid" => $trade_no,
                    "totalfee" => getPrice($total, 2, 'int')//价格扩大100
                ));
                exit("success");  //请不要修改或删除
            } else if ($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
                //该判断表示卖家已经发了货，但买家还没有做确认收货的操作
                exit("success");
            } else if ($_POST['trade_status'] == 'TRADE_FINISHED') {
                //该判断表示买家已经确认收货，这笔交易完成
                exit("success");
            } else {
                //其他状态判断
                exit("success");
            }
        }
        exit("fail");
    }

    //return_url
    public function preturn()
    {
        require_once THIRDPATH . "/payment/alipay/alipay_notify.class.php";
        $alipayNotify = new AlipayNotify($this->config);
        $verify_result = $alipayNotify->verifyReturn();

        if ($verify_result) {//验证成功
            //获取支付宝的通知返回参数，
            $out_trade_no = $_GET['out_trade_no']; //获取订单号
            $trade_no = $_GET['trade_no'];  //获取支付宝交易号
            $total_fee = $_GET['price'];   //获取总价格

            if ($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
                //该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
                PayTrade::setTrade($out_trade_no, array("status" => "WAIT_SEND",
                    "outtradeid" => $trade_no,
                    "totalfee" => getPrice($total_fee, 2, 'int')//价格扩大100
                ));
            }
            return array("tradeid" => $out_trade_no, "ret" => true);
        }
        return false;
    }

}
