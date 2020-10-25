<?php
/**
 * 
 * @authors 清月曦 (1604583867@qq.com)
 * @date    2017-03-14 11:02:22
 * @version $Id$
 */
namespace app\common\behavior;

class WxTplMsg
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

    	// self::$applet->ceshi();
    	// $applet = new \wechat\Applet();
    	dump(self::$applet);
    	// $applet->ceshi();
    }





}