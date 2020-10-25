<?php
namespace app\api\controller\user;
use app\api\controller\BaseController;

class RechargeController extends BaseController
{
    public $appUrl = "";
    public function _initialize()
    {
        if (request()->isOptions()){
            abort(json(true,200));
        }
        $this->appUrl = request()->root(true);
    }
    //充值记录列表
    public function index(){
        $user_id = $this->get_user_id();

        $map = array();
        $map['type'] = 1;
        $map['user_id'] = $user_id;
        $map['status'] = 1;
        
        if(input('param.page')){
            $rechargeList = model('Trade')->with('user')->where($map)->order('id', 'desc')->paginate();
        }else{
            $rechargeList = model('Trade')->with('user')->where($map)->order('id', 'desc')->select();
        }

        $data['rechargeList'] = $rechargeList;
        return json(['data' => $data, 'msg' => '充值记录列表', 'code' => 1]);
    }

    //小程序充值接口
    public function x_wx(){
        
        $user_id = $this->get_user_id();
        $money = input('param.money');
        $tradeid = '2'.date("ymdhis") . mt_rand(1, 9);
        $trade = model('Trade')->create([
            'tradeid'  =>  $tradeid,
            'user_id'  =>  $user_id,
            'money' =>  $money,
            'payment' =>  '小程序充值',
            'type'  =>  1
        ]);

        Vendor("WxPayPubHelper.WxPayPubHelper");

        $wxConfig = model("WxConfig")->find();
        $wx_pay = model("Payment")->where('type','wxpay')->find();
        //使用jsapi接口
        $jsApi = new \JsApi_pub($wxConfig["x_appid"], $wxConfig["x_appsecret"], $wx_pay["config"]["x_mchid"], $wx_pay["config"]["x_key"]);
        //=========步骤1：网页授权获取用户openid============
        if (input('?param.code')) {
            //获取code码，以获取openid
            $code = input('param.code');
            $jsApi->setCode($code);
            $openid = $jsApi->getOpenId();
        } else {
            abort(json(["msg" => "请带code访问", "code" => 0]));
        }
        //=========步骤1：网页授权获取用户openid============

        //使用统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub($wxConfig["x_appid"], $wxConfig["x_appsecret"], $wx_pay["config"]["x_mchid"], $wx_pay["config"]["x_key"]);
        //设置统一支付接口参数
        //设置必填参数
        //appid已填,商户无需重复填写
        //mch_id已填,商户无需重复填写
        //noncestr已填,商户无需重复填写
        //spbill_create_ip已填,商户无需重复填写
        //sign已填,商户无需重复填写
        $unifiedOrder->setParameter("openid", $openid);//商品描述
        $unifiedOrder->setParameter("body", '用户充值');//商品描述
        //自定义订单号，此处仅作举例
        $unifiedOrder->setParameter("out_trade_no", $trade["tradeid"]);//商户订单号
        $unifiedOrder->setParameter("total_fee", floatval($money) * 100);//总金额
        $unifiedOrder->setParameter("notify_url", $this->appUrl.url("/api/user/recharge/x_wxNotify"));//通知地址
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
     *小程序充值通知地址
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

            $this->payTrue($out_trade_no, $total_fee / 100, "小程序微信充值");
        }
    }
    //微信充值接口
    public function wx(){
        
        $user_id = $this->get_user_id();
        $money = input('param.money');
        $tradeid = '2'.date("ymdhis") . mt_rand(1, 9);
        $trade = model('Trade')->create([
            'tradeid'  =>  $tradeid,
            'user_id'  =>  $user_id,
            'money' =>  $money,
            'payment' =>  '微信充值',
            'type'  =>  1
        ]);

        Vendor("WxPayPubHelper.WxPayPubHelper");

        $wxConfig = model("WxConfig")->find();
        $wx_pay = model("Payment")->where('type','wxpay')->find();
        //使用jsapi接口
        $jsApi = new \JsApi_pub($wxConfig["appid"], $wxConfig["appsecret"], $wx_pay["config"]["mchid"], $wx_pay["config"]["key"]);
        //=========步骤1：网页授权获取用户openid============
        // if (input('?param.code')) {
        //     //获取code码，以获取openid
        //     $code = input('param.code');
        //     $jsApi->setCode($code);
        //     $openid = $jsApi->getOpenId();
        // } else {
        //     abort(json(["msg" => "请带code访问", "code" => 0]));
        // }
        $openid = model('OauthWx')->where('user_id',$user_id)->value('openid');
        if(!$openid){
            return json(['data' => false, 'msg' => '请使用微信登陆', 'code' => 0]);
        }
        //=========步骤1：网页授权获取用户openid============

        //使用统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub($wxConfig["appid"], $wxConfig["appsecret"], $wx_pay["config"]["mchid"], $wx_pay["config"]["key"]);
        //设置统一支付接口参数
        //设置必填参数
        //appid已填,商户无需重复填写
        //mch_id已填,商户无需重复填写
        //noncestr已填,商户无需重复填写
        //spbill_create_ip已填,商户无需重复填写
        //sign已填,商户无需重复填写
        $unifiedOrder->setParameter("openid", $openid);//商品描述
        $unifiedOrder->setParameter("body", '用户充值');//商品描述
        //自定义订单号，此处仅作举例
        $unifiedOrder->setParameter("out_trade_no", $trade["tradeid"]);//商户订单号
        $unifiedOrder->setParameter("total_fee", floatval($money) * 100);//总金额
        $unifiedOrder->setParameter("notify_url", $this->appUrl.url("/api/user/recharge/wxNotify"));//通知地址
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
     *微信充值通知地址
     */
    public function wxNotify()
    {
        
        Vendor("WxPayPubHelper.WxPayPubHelper");
        Vendor("WxPayPubHelper.log_");

        $wxConfig = model("WxConfig")->find();
        $wx_pay = model("Payment")->where('type','wxpay')->find();
        //使用通用通知接口
        $notify = new \Notify_pub($wxConfig["appid"], $wxConfig["appsecret"], $wx_pay["config"]["mchid"], $wx_pay["config"]["key"]);

        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];

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

            $this->payTrue($out_trade_no, $total_fee / 100, "微信充值");
        }
    }

    public function payTrue($out_trade_no, $total_fee, $payment)
    {
        $trade = model("Trade")->where("tradeid",$out_trade_no)->find();
        if(!$trade['status']){
            model("Trade")->where('tradeid',$out_trade_no)->setField('status',1);
            model("User")->where('id', $trade['user_id'])->setInc('money', $trade['money']);
        }
    }




}