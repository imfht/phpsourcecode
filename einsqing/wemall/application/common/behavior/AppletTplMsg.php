<?php
/**
 * 
 * @authors 清月曦 (1604583867@qq.com)
 * @date    2017-03-14 11:02:22
 * @version $Id$
 */
namespace app\common\behavior;

class AppletTplMsg
{
	public static $applet;

	public function run(&$params)
    {
    	// dump($params['id']);
    }
    public function init()
    {
    	$wxConfig = model("WxConfig")->find();
    	$options = array(
            'appid' => $wxConfig ["x_appid"], //填写高级调用功能的app id
            'appsecret' => $wxConfig ["x_appsecret"] //填写高级调用功能的密钥
        );
        self::$applet = new \wechat\Applet($options);
    }

	public function ceshi(&$params)
    {
    	$this->init();

    	$order_id = $params['order_id'];
    	$order = model('Order')->with('user.wx,detail.product.file')->find($order_id);

    	$detail = '';
    	foreach ($order['detail'] as $key => $value) {
            if($value['sku_name']){
                $detail .= '【'.$value['name'].'('.$value['sku_name'].')'.$value['price'].'元x'.$value['num'].份.'】';
            }else{
                $detail .= '【'.$value['name'].$value['price'].'元x'.$value['num'].'份】';
            }
        }

        $tplmsg = model('WxTplmsg')->where('template_id_short','AT0009')->find();

        $msg = array();
        // $msg["touser"] = $order['user']['wx']["openid"];
        $msg["touser"] = 'oQgwY0Zpij3IXhlCqcvQT8wgl71Q';
        $msg["template_id"] = $tplmsg['template_id'];
        $msg["url"] = "";
        $msg["topcolor"] = "";
        $msg["form_id"] = "123456";
        $msg["data"] = array(
            "keyword1" => array(
                "value" => $order["orderid"],
                "color" => "#000000"
            ),
            "keyword2" => array(
                "value" => $detail,
                "color" => "#000000"
            ),
            "keyword3" => array(
                "value" => $order['payment'],
                "color" => "#000000"
            ),
            "keyword4" => array(
                "value" => $order["totalprice"].'元',
                "color" => "#000000"
            ),
            "keyword5" => array(
                "value" => $tplmsg["remark"],
                "color" => "#ff0000"
            ),
        );

        self::$applet->sendTemplateMessage($msg);
    }





}