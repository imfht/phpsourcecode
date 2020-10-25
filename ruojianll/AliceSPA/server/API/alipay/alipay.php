<?php

require_once("alipay.config.php");
require_once("lib/alipay_submit.class.php");
require_once("lib/alipay_notify.class.php");
$app->post('/alipay/formData',function()use($app,$alipay_config){

   $user = getCurrentUser($app);
    $uti = $app->utility;
    if(!hasPermission($user,PERMISSION_USER)){
        $uti->addError(ERROR_NO_PERMISSION);
        return;
    };
    $data = $uti->getPostData(array(
       'order_id'
    ));
    if($data == false){
        return;
    }
    $order = RjOrder::findFirst(array('user_id=:uid: AND id=:id: AND state=:state:','bind'=>array(
        'uid' => $user['id'],
        'id' => $data->order_id,
        'state' => ORDER_STATE_UNPAID
    )));
    if($order === false){
        $uti->addError(ERROR_NO_CURRENT_RECORD);
        return;
    }

    $address = $order->address;
    $orderItems = $order->orderItems;
    $subject = "";
    foreach($orderItems as $item){
        $product = $item->product;
        $subject = $subject . $product->name . " X " . $item->number . " ";
    }


        //支付类型
    $payment_type = "1";
    //必填，不能修改
    //服务器异步通知页面路径
    $notify_url = $alipay_config['notify_url'];
    //需http://格式的完整路径，不能加?id=123这类自定义参数

    //页面跳转同步通知页面路径
    $return_url = $alipay_config['return_url'];
    //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

    //商户订单号
    $out_trade_no = $order->id;
    //商户网站订单系统中唯一订单号，必填

    //订单名称
    $subject = $subject;
    //必填

    //付款金额
    $price = $order->expect_pay;
    //必填

    //商品数量
    $quantity = "1";
    //必填，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品
    //物流费用
    $logistics_fee = "0.00";
    //必填，即运费
    //物流类型
    $logistics_type = "EXPRESS";
    //必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
    //物流支付方式
    $logistics_payment = "SELLER_PAY";
    //必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
    //订单描述

//    $body = $_POST['WIDbody'];
//    //商品展示地址
//    $show_url = $_POST['WIDshow_url'];
    //需以http://开头的完整路径，如：http://www.商户网站.com/myorder.html

    //收货人姓名
    $receive_name = $address->name;
    //如：张三

    //收货人地址
    $receive_address = $address->detail;
    //如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号

//    //收货人邮编
//    $receive_zip = $_POST['WIDreceive_zip'];
//    //如：123456

//    //收货人电话号码
//    $receive_phone = $_POST['WIDreceive_phone'];
//    //如：0571-88158090

    //收货人手机号码
    $receive_mobile = $address->phone;
    //如：13312341234


    /************************************************************/

//构造要请求的参数数组，无需改动

    $parameter = array(
        "service" => "create_partner_trade_by_buyer",

        "partner" => trim($alipay_config['partner']),
        "seller_email" => trim($alipay_config['seller_email']),
        "payment_type"	=> $payment_type,
        "notify_url"	=> $notify_url,
        "return_url"	=> $return_url,
        "out_trade_no"	=> $out_trade_no,
        "subject"	=> $subject,
        "price"	=> $price,
        "quantity"	=> $quantity,
        "logistics_fee"	=> $logistics_fee,
        "logistics_type"	=> $logistics_type,
        "logistics_payment"	=> $logistics_payment,
//        "body"	=> $body,
//        "show_url"	=> $show_url,
//            "receive_name"	=> $receive_name,
//            "receive_address"	=> $receive_address,
//        "receive_zip"	=> $receive_zip,
//        "receive_phone"	=> $receive_phone,
    //        "receive_mobile"	=> $receive_mobile,
        "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
    );
    $uti->setSuccessTrue();
    $alipaySubmit = new AlipaySubmit($alipay_config);
    $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");

    $uti->setItem('formData',$html_text);
});

$app->get('/alipay/return_rul',function()use($app,$alipay_config){
$app->utility->closeApi();
    echo '<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>若简臻品（支付确认）</title></head><body>';

    //计算得出通知验证结果
    $alipayNotify = new AlipayNotify($alipay_config);
    $verify_result = $alipayNotify->verifyReturn();

    if($verify_result ||  true) {//验证成功
        $order = RjOrder::findFirst(array('id=:id: ','bind' => array(
            'id'=>$_GET['out_trade_no']
        )));
        if($order === false){
            echo "<h1>您的支付失败，请联系客服。1</h1>";
        }
        else{
            if($order->state == ORDER_STATE_UNPAID){
                if($order->save(array('state'=>ORDER_STATE_UNSENT,'trad_id'=>$_GET['trade_no']))){
                    echo "<h1>恭喜！支付成功，请等待收货。<a href='http://www.ruojian.me'>返回</a></h1>";
                }else{
                    echo "<h1>您的支付失败，请联系客服。2</h1>";
                };
            }
            else{
                echo "<h1>恭喜！支付成功，请等待收货。<a href='http://www.ruojian.me'>返回</a></h1>";
            }
        }
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //请在这里加上商户的业务逻辑程序代码

        //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
        //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

        //商户订单号



        //支付宝交易号
//
//        $trade_no = $_GET['trade_no'];
//
//        //交易状态
//        $trade_status = $_GET['trade_status'];
//
//
//        if($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
//            //判断该笔订单是否在商户网站中已经做过处理
//            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//            //如果有做过处理，不执行商户的业务程序
//        }
//        else {
//            echo "trade_status=".$_GET['trade_status'];
//        }
//
//        echo "验证成功<br />";
//        echo "trade_no=".$trade_no;

        //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    }
    else {
//        //验证失败
//        //如要调试，请看alipay_notify.php页面的verifyReturn函数
//        echo "验证失败";
        echo "<h1>您的支付失败，请联系客服。3</h1>";
    }
    echo '</body></html>';
});



$app->post('/alipay/notify_url',function()use($app,$alipay_config){
    $app->utility->closeApi();

    //计算得出通知验证结果
    $alipayNotify = new AlipayNotify($alipay_config);
    $verify_result = $alipayNotify->verifyNotify();

    if($verify_result) {//验证成功


        $order = RjOrder::findFirst(array('id=:id:','bind' => array(
            'id'=>$_POST['out_trade_no'],
        )));
        if($order === false){

            echo "fail";
        }
        if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS'){
            if($order->state==ORDER_STATE_UNPAID){
                if($order->save(array('state'=>ORDER_STATE_UNSENT,'trad_id'=>$_POST['trade_no']))){

                    echo "success";
                }else{

                    echo "fail";
                };
            }
            else{

                echo "success";
            }

        }
        else{

            echo "success";
        }
        //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    }
    else {
        //验证失败

        echo "fail";
        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
});

function setSendAlipay($WIDtrade_no,$WIDlogistics_name,$WIDinvoice_no,$WIDtransport_type="EXPRESS"){
    global $alipay_config;
    logResult('send');
    //支付宝交易号
    $trade_no = $WIDtrade_no;
    //必填

    //物流公司名称
    $logistics_name = $WIDlogistics_name;
    //必填

    //物流发货单号

    $invoice_no = $WIDinvoice_no;
    //物流运输类型
    $transport_type = $WIDtransport_type;
    //三个值可选：POST（平邮）、EXPRESS（快递）、EMS（EMS）


    /************************************************************/

    //构造要请求的参数数组，无需改动
    $parameter = array(
        "service" => "send_goods_confirm_by_platform",
        "partner" => trim($alipay_config['partner']),
        "trade_no" => $trade_no,
        "logistics_name" => $logistics_name,
        "invoice_no" => $invoice_no,
        "transport_type" => $transport_type,
        "_input_charset" => trim(strtolower($alipay_config['input_charset']))
    );

    //建立请求
    $alipaySubmit = new AlipaySubmit($alipay_config);
    $html_text = $alipaySubmit->buildRequestHttp($parameter);
    //解析XML
    //注意：该功能PHP5环境及以上支持，需开通curl、SSL等PHP配置环境。建议本地调试时使用PHP开发软件
    $doc = new DOMDocument();
    $doc->loadXML($html_text);

    //请在这里加上商户的业务逻辑程序代码

    //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

//    //解析XML
    if (!empty($doc->getElementsByTagName("is_success")->item(0)->nodeValue)) {
        $alipay = $doc->getElementsByTagName("is_success")->item(0)->nodeValue;
        return $alipay;
    }

};