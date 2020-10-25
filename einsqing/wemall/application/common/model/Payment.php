<?php
namespace app\common\model;

use think\Model;

class Payment extends Model
{
	protected $resultSetType = 'collection';
	protected $autoWriteTimestamp = 'timestamp';
	// 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    
	protected $type = [
        'config'      =>  'json',
    ];

    /**
	 * 用户登录认证
	 * @param  string  $username 用户名
	 * @param  string  $password 用户密码
	 * @param  integer $type     用户名类型 （1-微信，2-小程序，3-支付宝，4-UID）
	 * @return integer           登录成功-用户ID，登录失败-错误编号
	 */
	public function pay($id, $type = 1){
		$trade = model('Trade')->find($id);
		switch ($type) {
			case 1:
				$map['username'] = $username;
				break;
			case 2:
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
		        $unifiedOrder->setParameter("total_fee", floatval($trade["money"]) * 100);//总金额
		        $unifiedOrder->setParameter("notify_url", request()->root(true).url("api/user/recharge/x_wxNotify"));//通知地址
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
                abort(json(['data' => $data, 'msg' => '获取成功', 'code' => 1]));
				break;
			case 3:
				$map['mobile'] = $username;
				break;
			case 4:
				$map['id'] = $username;
				break;
			default:
				return 0; //参数错误
		}
	}

}