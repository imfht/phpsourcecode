<?php
namespace app\api\controller;
use app\api\controller\BaseController;

class PayController extends BaseController
{
    public $appUrl = "";
    public function _initialize()
    {
        if (request()->isOptions()){
            abort(json(true,200));
        }
        $this->appUrl = request()->root(true);
    }

    //小程序微信支付
    public function x_wxpay()
    {
        $user_id = $this->get_user_id();
        $id = input('param.id');

        $order = model('Order')->with('detail')->find($id);

        Vendor("WxPayPubHelper.WxPayPubHelper");

        $wxConfig = model("WxConfig")->find();
        $wx_pay = model("Payment")->where('type','wxpay')->find();
        //使用jsapi接口
        $jsApi = new \JsApi_pub($wxConfig["x_appid"], $wxConfig["x_appsecret"], $wx_pay["config"]["x_mchid"], $wx_pay["config"]["x_key"]);
        //=========步骤1：网页授权获取用户openid============
        //通过code获得openid
        // if (input('?param.code')) {
        //     //获取code码，以获取openid
        //     $code = input('param.code');
        //     $jsApi->setCode($code);
        //     $openid = $jsApi->getOpenId();
        // } else {
        //     return json(['data' => false, 'msg' => '请带code访问', 'code' => 0]);
        // }

        $openid = model('OauthApplet')->where('user_id',$user_id)->value('openid');
        if(!$openid){
            return json(['data' => false, 'msg' => '请使用微信登陆', 'code' => 0]);
        }

        //使用统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub($wxConfig["x_appid"], $wxConfig["x_appsecret"], $wx_pay["config"]["x_mchid"], $wx_pay["config"]["x_key"]);
        $detail = '';
        foreach ($order['detail'] as &$d) {
            $detail .= '['.$d['name'].'¥'.$d['price'].'*'.$d['num'].']';
        }
        //设置统一支付接口参数
        //设置必填参数
        //appid已填,商户无需重复填写
        //mch_id已填,商户无需重复填写
        //noncestr已填,商户无需重复填写
        //spbill_create_ip已填,商户无需重复填写
        //sign已填,商户无需重复填写
        $unifiedOrder->setParameter("openid", $openid);//商品描述
        $unifiedOrder->setParameter("body", $detail);//商品描述
        //自定义订单号，此处仅作举例
        $unifiedOrder->setParameter("out_trade_no", $order["orderid"]);//商户订单号
        $unifiedOrder->setParameter("total_fee", floatval($order["totalprice"]) * 100);//总金额
        $unifiedOrder->setParameter("notify_url", $this->appUrl . url("api/pay/x_wxNotify"));//通知地址
        $unifiedOrder->setParameter("trade_type", "JSAPI");//交易类型

        //非必填参数，商户可根据实际情况选填
        //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
        //$unifiedOrder->setParameter("device_info","XXXX");//设备号 
        //$unifiedOrder->setParameter("attach","XXXX");//附加数据 
        //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
        //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
        //$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
        //$unifiedOrder->setParameter("openid","XXXX");//用户标识
        //$unifiedOrder->setParameter("product_id","XXXX");//商品ID
        $prepay_id = $unifiedOrder->getPrepayId();

        //=========步骤3：使用jsapi调起支付============
        $jsApi->setPrepayId($prepay_id);

        $jsApiParameters = $jsApi->getParameters();

        $data['jsApiParameters'] = json_decode($jsApiParameters);

        return json(['data' => $data, 'msg' => '获取成功', 'code' => 1]);
    }
    //微信支付
    public function wxpay()
    {
        $user_id = $this->get_user_id();
        $id = input('param.id');
        $order = model('Order')->with('detail')->find($id);
        
        Vendor("WxPayPubHelper.WxPayPubHelper");

        $wxConfig = model("WxConfig")->find()->toArray();
        $wx_pay = model("Payment")->where('type','wxpay')->find();
        //使用jsapi接口
        $jsApi = new \JsApi_pub($wxConfig["appid"], $wxConfig["appsecret"], $wx_pay["config"]["mchid"], $wx_pay["config"]["key"]);
        //=========步骤1：网页授权获取用户openid============
        $openid = model('OauthWx')->where('user_id',$user_id)->value('openid');
        if(!$openid){
            return json(['data' => false, 'msg' => '请使用微信登陆', 'code' => 0]);
        }
        //通过code获得openid
        // if (!input('?param.code')) {
        //     $redirect = input('param.redirect');
        //     //触发微信返回code码
        //     // $url = $jsApi->createOauthUrlForCode($this->appUrl);
        //     // Header("Location: $url");
        //     $url = 'http://weixin.wemallshop.com/oauth-proxy.html?appid='.$wxConfig["appid"].'&scope=snsapi_userinfo&state=&redirect_uri='.$redirect;
        //     $data['url'] = $url;
        //     return json(['data' => $data, 'msg' => '用此url去换code', 'code' => 0]);
        // } else {
        //     //获取code码，以获取openid
        //     $code = input('param.code');
        //     $jsApi->setCode($code);
        //     $openid = $jsApi->getOpenId();
        // }

        //=========步骤2：使用统一支付接口，获取prepay_id============
        //使用统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub($wxConfig["appid"], $wxConfig["appsecret"], $wx_pay["config"]["mchid"], $wx_pay["config"]["key"]);
        $detail = '';
        foreach ($order['detail'] as &$d) {
            $detail .= '['.$d['name'].'¥'.$d['price'].'*'.$d['num'].']';
        }
        //设置统一支付接口参数
        //设置必填参数
        //appid已填,商户无需重复填写
        //mch_id已填,商户无需重复填写
        //noncestr已填,商户无需重复填写
        //spbill_create_ip已填,商户无需重复填写
        //sign已填,商户无需重复填写
        $unifiedOrder->setParameter("openid", "$openid");//商品描述
        $unifiedOrder->setParameter("body", $detail);//商品描述
        //自定义订单号，此处仅作举例
        $unifiedOrder->setParameter("out_trade_no", $order["orderid"]);//商户订单号
        $unifiedOrder->setParameter("total_fee", floatval($order["totalprice"]) * 100);//总金额
        $unifiedOrder->setParameter("notify_url", $this->appUrl.url("api/pay/wxNotify"));//通知地址
        $unifiedOrder->setParameter("trade_type", "JSAPI");//交易类型

        //非必填参数，商户可根据实际情况选填
        //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
        //$unifiedOrder->setParameter("device_info","XXXX");//设备号 
        //$unifiedOrder->setParameter("attach","XXXX");//附加数据 
        //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
        //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
        //$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
        //$unifiedOrder->setParameter("openid","XXXX");//用户标识
        //$unifiedOrder->setParameter("product_id","XXXX");//商品ID

        $prepay_id = $unifiedOrder->getPrepayId();
        //=========步骤3：使用jsapi调起支付============
        $jsApi->setPrepayId($prepay_id);

        $jsApiParameters = $jsApi->getParameters();

        $data['jsApiParameters'] = json_decode($jsApiParameters);

        return json(['data' => $data, 'msg' => '获取成功', 'code' => 1]);
    }

    /**
     *微信扫码支付
     */
    public function wxQrcodePay()
    {
        $id = input('param.id');
        $order = model('Order')->with('detail')->find($id);

        Vendor("WxPayPubHelper.WxPayPubHelper");
        $wxConfig = model("WxConfig")->find()->toArray();
        
        $wx_pay = model("Payment")->where('type','wxpay')->find()->toArray();
        //使用统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub($wxConfig["appid"], $wxConfig["appsecret"], $wx_pay["config"]["mchid"], $wx_pay["config"]["key"]);
        $detail = '';
        foreach ($order['detail'] as &$d) {
            $detail .= '['.$d['name'].'¥'.$d['price'].'*'.$d['num'].']';
        }
        
        //设置统一支付接口参数
        //设置必填参数
        //appid已填,商户无需重复填写
        //mch_id已填,商户无需重复填写
        //noncestr已填,商户无需重复填写
        //spbill_create_ip已填,商户无需重复填写
        //sign已填,商户无需重复填写
        $unifiedOrder->setParameter("body", $order["orderid"]);//商品描述

        $unifiedOrder->setParameter("out_trade_no", $order["orderid"]);//商户订单号
        $unifiedOrder->setParameter("total_fee", floatval($order["totalprice"]) * 100);//总金额
        $unifiedOrder->setParameter("notify_url",$this->appUrl.url("api/pay/wxNotify"));//通知地址
        $unifiedOrder->setParameter("trade_type", "NATIVE");//交易类型
        //非必填参数，商户可根据实际情况选填
        //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
        //$unifiedOrder->setParameter("device_info","XXXX");//设备号 
        //$unifiedOrder->setParameter("attach","XXXX");//附加数据 
        //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
        //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
        //$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
        //$unifiedOrder->setParameter("openid","XXXX");//用户标识
        //$unifiedOrder->setParameter("product_id","XXXX");//商品ID

        //获取统一支付接口结果
        $unifiedOrderResult = $unifiedOrder->getResult();

        //商户根据实际情况设置相应的处理流程
        if ($unifiedOrderResult["return_code"] == "FAIL") {
            //商户自行增加处理流程
            echo "通信出错：" . $unifiedOrderResult['return_msg'] . "<br>";
        } elseif ($unifiedOrderResult["result_code"] == "FAIL") {
            //商户自行增加处理流程
            echo "错误代码：" . $unifiedOrderResult['err_code'] . "<br>";
            echo "错误代码描述：" . $unifiedOrderResult['err_code_des'] . "<br>";
        } elseif ($unifiedOrderResult["code_url"] != NULL) {
            //从统一支付接口获取到code_url
            $code_url = $unifiedOrderResult["code_url"];

            //商户自行增加处理流程
            //......
            vendor("phpqrcode.phpqrcode");
            // 纠错级别：L、M、Q、H
            $level = 'L';
            // 点的大小：1到10,用于手机端4就可以了
            $size = 4;
            // 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
            \QRcode::svg($code_url, false, $level, $size, 1, false);
            // \QRcode::png($code_url, false, $level, $size);
        }
    }

    /**
     *小程序支付通知地址
     */
    public function x_wxNotify()
    {
        Vendor("WxPayPubHelper.WxPayPubHelper");
        Vendor("WxPayPubHelper.log_");
        
        $wxConfig = model("WxConfig")->find();
        $wx_pay = model("Payment")->where('type','wxpay')->find();
        //使用通用通知接口
        $notify = new \Notify_pub($wxConfig["x_appid"], $wxConfig["x_appsecret"], $wx_pay["config"]["x_mchid"], $wx_pay["config"]["x_key"]);

        //存储微信的回调
        if (version_compare(PHP_VERSION, '7.0.0', '>')){
            $xml = file_get_contents("php://input");//php7下
        }else{
            $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        }
        $notify->saveData($xml);
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if ($notify->checkSign() == FALSE) {
            $notify->setReturnParameter("return_code", "FAIL");//返回状态码
            $notify->setReturnParameter("return_msg", "签名失败");//返回信息
        } else {
            $notify->setReturnParameter("return_code", "SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();
        // echo $returnXml;

        //以log文件形式记录回调信息
        $log_ = new \Log_();
        $log_name = "./data/notify_url.log";//log文件路径
        $log_->log_result($log_name, "【接收到的notify通知】:\n" . $xml . "\n");

        //==商户根据实际情况设置相应的处理流程，此处仅作举例=======
        if ($notify->checkSign() == TRUE) {
            if ($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $log_->log_result($log_name, "【通信出错】:\n" . $xml . "\n");
            } elseif ($notify->data["result_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $log_->log_result($log_name, "【业务出错】:\n" . $xml . "\n");
            } else {
                //此处应该更新一下订单状态，商户自行增删操作
                $log_->log_result($log_name, "【支付成功】:\n" . $xml . "\n");
            }

            $xml = $notify->xmlToArray($xml);
            // 商户订单号
            $out_trade_no = $xml ['out_trade_no'];
            $total_fee = $xml ['total_fee'];
            $openid = $xml ['openid'];
            // 判断该笔订单是否在商户网站中已经做过处理
            // 如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            // 如果有做过处理，不执行商户的业务程序
            $this->payTrue($out_trade_no, $total_fee / 100, "小程序微信支付");
        }
    }

    /**
     *公众号支付通知地址
     */
    public function wxNotify()
    {
        Vendor("WxPayPubHelper.WxPayPubHelper");
        Vendor("WxPayPubHelper.log_");

        $wxConfig = model("WxConfig")->find()->toArray();
        $wx_pay = model("Payment")->where('type','wxpay')->find()->toArray();
        //使用通用通知接口
        $notify = new \Notify_pub($wxConfig["appid"], $wxConfig["appsecret"], $wx_pay["config"]["mchid"], $wx_pay["config"]["key"]);

        //存储微信的回调
        if (version_compare(PHP_VERSION, '7.0.0', '>')){
            $xml = file_get_contents("php://input");//php7下
        }else{
            $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        }
        $notify->saveData($xml);

        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if ($notify->checkSign() == FALSE) {
            $notify->setReturnParameter("return_code", "FAIL");//返回状态码
            $notify->setReturnParameter("return_msg", "签名失败");//返回信息
        } else {
            $notify->setReturnParameter("return_code", "SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();
        // echo $returnXml;

        //以log文件形式记录回调信息
        $log_ = new \Log_();
        $log_name = "./data/notify_url.log";//log文件路径
        $log_->log_result($log_name, "【接收到的notify通知】:\n" . $xml . "\n");

        //==商户根据实际情况设置相应的处理流程，此处仅作举例=======
        if ($notify->checkSign() == TRUE) {
            if ($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $log_->log_result($log_name, "【通信出错】:\n" . $xml . "\n");
            } elseif ($notify->data["result_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $log_->log_result($log_name, "【业务出错】:\n" . $xml . "\n");
            } else {
                //此处应该更新一下订单状态，商户自行增删操作
                $log_->log_result($log_name, "【支付成功】:\n" . $xml . "\n");
            }

            $xml = $notify->xmlToArray($xml);
            // 商户订单号
            $out_trade_no = $xml ['out_trade_no'];
            $total_fee = $xml ['total_fee'];
            $openid = $xml ['openid'];
            // 判断该笔订单是否在商户网站中已经做过处理
            // 如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            // 如果有做过处理，不执行商户的业务程序

            $this->payTrue($out_trade_no, $total_fee / 100, "微信支付");
        }
    }

    public function payTrue($out_trade_no, $total_fee, $payment)
    {
        $trade = model("Trade")->where("tradeid",$out_trade_no)->find();
        if (!$trade) {
            $order = model("Order")->where("orderid",$out_trade_no)->find();
            
            model("Order")->where('id',$order['id'])->setField('pay_status',1);

            if ($total_fee == "") {
                $total_fee = $order["totalprice"];
            }

            $this->addTrade($order["user_id"], floatval($total_fee), $out_trade_no, $payment);
        }
    }

    public function addTrade($userId, $money, $tradeid, $payment)
    {
        $order = model("Order")->where("orderid",$tradeid)->find();
        //用户积分增加
        model("User")->where('id', $order['user_id'])->setInc('score', $order['totalscore']);

        action('admin/WechatController/sendTplMsgPay',['order_id' => $order["id"]]);

        model("Trade")->create([
            "user_id" => $userId,
            "money" => $money,
            "tradeid" => $tradeid,
            "status" => 1,
            "payment" => $payment
        ]);
    }

    /**
     * @return array
     */
    public function alipayInit()
    {
        $alipay = model("Payment")->where('type','alipay')->find();
        $config = array(
            // 即时到账方式
            'payment_type' => '1',
            // 传输协议
            'transport' => 'http',
            // 编码方式
            'input_charset' => strtolower('utf-8'),
            // 签名方法
            'sign_type' => strtoupper('MD5'),
            // 支付完成异步通知调用地址
            'notify_url' => $this->appUrl . url('api/pay/alipayNotifyUrl'),
            // 支付完成同步返回地址
            'return_url' => $this->appUrl . url('api/pay/alipayReturnUrl', array("id" => input('param.id'))),
            // 证书路径
            'cacert' => DATA_PATH . 'cacert.pem',
            // 支付宝商家 ID
            'partner' => $alipay['config']['partner'],
            // 支付宝商家 KEY
            'key' => $alipay['config']['key'],
            // 支付宝商家注册邮箱
            'seller_email' => $alipay['config']['account']
        );
        return $config;
    }

    //支付宝支付
    public function alipay()
    {
        $id = input('param.id');
        $order = model('Order')->with('user,contact,delivery,detail.product.file')->find($id);

        Vendor("qingyuexi.Alipay.Alipay");

        $out_trade_no = $order["orderid"];
        $subject = $order["orderid"];
        $total_fee = floatval($order["totalprice"]);
        $body = $order["orderid"];
        $show_url = $this->appUrl;
        $anti_phishing_key = "";
        $exter_invoke_ip = "";

        /************************************************************/
        $is_mobile = request()->isMobile() ? true :false;
        // $is_mobile = true;
        
        $alipay_config = $this->alipayInit();
        $alipay = new \Alipay($alipay_config, $is_mobile);

        if ($is_mobile) {
            $params = $alipay->prepareMobileTradeData(array(
                'out_trade_no' => $out_trade_no,
                'subject' => $subject,
                'body' => $body,
                'total_fee' => $total_fee,
                'merchant_url' => $this->appUrl . "/api/index/index#/order/" . $id,
                'req_id' => date('Ymdhis-')
            ));
            $url = $alipay->buildRequestUrl($params);
            $this->assign("url", $url);
            $this->view->engine->layout(false);
            return view();
            // $data['url'] = $url;
            // return json(['data' => $data, 'msg' => '支付链接', 'code' => 1]);
        } else {
            $html = $alipay->buildRequestFormHTML(array(
                "service" => "create_direct_pay_by_user",
                "partner" => trim($alipay_config['partner']),
                "payment_type" => $alipay_config['payment_type'],
                "notify_url" => $alipay_config['notify_url'],
                "return_url" => $alipay_config['return_url'],
                "seller_id" => $alipay_config['partner'],
                "out_trade_no" => $out_trade_no,
                "subject" => $subject,
                "total_fee" => $total_fee,
                "body" => $body,
                "show_url" => $show_url,
                "anti_phishing_key" => $anti_phishing_key,
                "exter_invoke_ip" => $exter_invoke_ip,
                "_input_charset" => trim(strtolower($alipay_config['input_charset']))
            ));
            echo $html;
        }
    }    

    /**
     *
     */
    public function alipayNotifyUrl()
    {
        
        Vendor("qingyuexi.Alipay.Alipay");
        if(isset($_POST['notify_data'])){
            //手机支付
            $is_mobile = true;
        }else{
            //网页支付
            $is_mobile = false;
        }
        $alipay_config = $this->alipayInit();
        $alipay = new \Alipay($alipay_config, $is_mobile);
        $verify_result = $alipay->verifyCallback();

        if ($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            if(isset($_POST['notify_data'])){
                $_POST = simplest_xml_to_array($_POST['notify_data']);
            }
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];


            if ($_POST['trade_status'] == 'TRADE_FINISHED') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //该种交易状态只在两种情况下出现
                //1、开通了普通即时到账，买家付款成功后。
                //2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");

                $this->payTrue($out_trade_no, "", "支付宝支付");
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";        //请不要修改或删除

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            echo "fail";

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }

    /**
     * 仅使用支付宝异步通知
     */
    public function alipayReturnUrl()
    {
        $url = $this->appUrl . "/app/index/index#/order/" . input('param.id');
        header("location: $url");
    }

    //余额支付
    public function balance()
    {
        $id = input('param.id');
        $user_id = $this->get_user_id();//用户id
        $order = model('Order')->find($id);
        $user = model('User')->find($user_id);

        $balance = floatval($user["money"]) - floatval($order['totalprice']);
        if ($balance >= 0) {
            model('User')->where('id',$user_id)->update(['money' => $balance]);
            //用户积分增加
            model("User")->where('id', $order['user_id'])->setInc('score', $order['totalscore']);

            $this->payTrue($order['orderid'], "", "余额支付");
            $info = ['data' => false, 'msg' => '余额支付成功', 'code' => 1];
        } else {
            $info = ['data' => false, 'msg' => '您的余额不足', 'code' => 0];
        }

        return json($info);
    }

    //微信封杀支付宝跳转链接
    public function pay()
    {
        $this->view->engine->layout(false);
        return view();
    }



}