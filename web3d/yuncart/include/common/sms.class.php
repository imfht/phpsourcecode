<?php

defined('IN_CART') or die;

require_once COMMONPATH . "/http.class.php";

/**
 *
 *
 * 短信发送类
 *
 */
class Sms
{

    protected $http; //http访问类
    protected $url = "http://sms.yuncart.com"; //sms短信接口
    protected $smsphone = ""; //sms登录手机号
    protected $smspass = ""; //sms登录密码
    protected $error = "";

    /**
     *
     *
     * 构造函数
     *
     */
    public function __construct($smsphone = "", $smspass = "")
    {
        $this->http = new Http();
        $this->smsphone = $smsphone;
        $this->smspass = $smspass;
    }

    /**
     *
     *
     * 验证用户是否正确，如果正确，返回用户信息，
     *
     */
    public function getUser()
    {
        if (!$this->smsphone || !$this->smspass)
            return null;
        $ret = $this->http->post($this->url . "/sms/getuser", array("smsphone" => $this->smsphone, "smspass" => $this->smspass));
        if (!$ret)
            return null;
        $ret = json_decode($ret, true);
        if (isset($ret['user'])) {
            return $ret['user'];
        } elseif (isset($ret['error'])) {
            $this->error = $ret['error'];
        }
        return null;
    }

    /**
     *
     *
     * 发送短信
     *
     */
    public function send($phone, $content)
    {
        if (!$this->smsphone || !$this->smspass)
            return null;
        $ret = $this->http->post($this->url . "/sms/send", array("phone" => $phone, "content" => $content,
            "smsphone" => $this->smsphone, "smspass" => $this->smspass));
        if (!$ret)
            return null;
        $ret = json_decode($ret, true);
        if (isset($ret['result']) && $ret['result'] == 'success') {
            return true;
        } elseif (isset($ret['error'])) {
            $this->error = $ret['error'];
        }
        return false;
    }

    /**
     *
     *
     * 发送短信
     *
     */
    public function getError()
    {
        return $this->error;
    }

}
