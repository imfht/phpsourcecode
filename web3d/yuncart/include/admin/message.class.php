<?php

defined('IN_CART') or die;

/**
 *
 * 邮件短信设置
 * 
 */
class Message extends Base
{

    /**
     *
     * 邮件短信设置
     * 
     */
    public function index()
    {
        $this->data["refer"] = isset($_GET["refer"]) ? trim($_GET["refer"]) : 'email';
        $this->data["messageset"] = DB::getDB()->selectkv("config", "key", "val", "type='messageset'");
        if (!empty($this->data["messageset"]["smsphone"]) && !empty($this->data["messageset"]["smspass"])) {
            include_once COMMONPATH . "/sms.class.php";
            $sms = new Sms($this->data["messageset"]["smsphone"], $this->data["messageset"]["smspass"]);
            $this->data['user'] = $sms->getUser();
        }
        $this->output("message_index");
    }

    /**
     *
     * 保存邮件 对应messageset
     * 
     */
    public function messagesave()
    {
        //接受参数
        $data["smtphost"] = trim($_POST["smtphost"]);
        $data["smtpport"] = trim($_POST["smtpport"]);
        $data["smtpuser"] = trim($_POST["smtpuser"]);
        $data["smtppass"] = trim($_POST["smtppass"]);
        $data["smsphone"] = trim($_POST["smsphone"]);
        $data["smspass"] = trim($_POST["smspass"]);

        //替换配置
        $replacedata = array();
        foreach ($data as $key => $val) {
            $replacedata[] = array("key" => $key, "val" => $val, "type" => 'messageset');
        }
        $this->adminlog("al_message");
        if ($replacedata)
            DB::getDB()->replaceMulti("config", $replacedata);

        $this->setHint(__("set_success", __('messageset')));
    }

    /**
     *
     * 检测短信是否正确
     * 
     */
    public function check()
    {
        $type = trim($_POST["type"]);
        $ret = false;
        if ($type == "email") {
            $smtphost = trim($_POST["smtphost"]);
            $smtpport = trim($_POST["smtpport"]);
            $smtpuser = trim($_POST["smtpuser"]);
            $smtppass = trim($_POST["smtppass"]);
            $testemail = trim($_POST["testemail"]);
            require COMMONPATH . "/send.class.php";
            $sendmail = new SendEmail($smtphost, $smtpport, $smtpuser, $smtppass);
            $subject = __("testemail");
            $content = __("testemail");
            $ret = $sendmail->send($testemail, $subject, $content);
            exit($ret ? "success" : $sendmail->getError());
        } else if ($type == "sms") {
            $smsphone = trim($_POST["smsphone"]);
            $smspass = trim($_POST["smspass"]);
            $testsms = trim($_POST["testsms"]);

            include_once COMMONPATH . "/sms.class.php";
            $sms = new Sms($smsphone, $smspass);
            $content = __("testsms");
            $ret = $sms->send($testsms, $content);
            exit($ret ? "success" : $sms->getError());
        }
    }

    /**
     *
     * 模版设置 
     * 
     */
    public function tplset()
    {
        $this->data["message_list"] = DB::getDB()->select("message_set", "*");
        $this->output("messagetpl_index");
    }

    /**
     *
     * 保存模版设置 
     * 
     */
    public function tplsetsave()
    {
        $codes = $_POST["code"];
        $emails = isset($_POST["email"]) ? $_POST["email"] : array();
        $mobiles = isset($_POST["mobile"]) ? $_POST["mobile"] : array();
        $letters = isset($_POST["letter"]) ? $_POST["letter"] : array();

        foreach ($codes as $code) {
            $email = isset($emails[$code]) ? intval($emails[$code]) : 0;
            $mobile = isset($mobiles[$code]) ? intval($mobiles[$code]) : 0;
            $letter = isset($letters[$code]) ? intval($letters[$code]) : 0;
            DB::getDB()->update("message_set", array("email" => $email, "mobile" => $mobile, "letter" => $letter), "code='$code'");
        }
        $this->adminlog("al_message_tpl");
        $this->setHint(__("set_success", __('messagetpl')), "message_tplset");
    }

    /**
     *
     * 修改模版 
     * 
     */
    public function tpledit()
    {
        if (ispostreq()) {
            $code = trim($_POST["code"]);
            $content = str_replace(array("｛", "｝"), array("{", "}"), trim($_POST["content"]));
            $method = trim($_POST["method"]);
            !in_array($method, array("mobile", "email", "letter")) && $method = 'mobile';

            $this->adminlog("al_message_tpl2", array("code" => $code, "method" => __($method)));
            DB::getDB()->update("message_set", "{$method}cont='$content'", "code='$code'");
            $this->setHint(__("set_success", __('tpl')), "message_tplset");
        } else {
            $method = trim($_GET["method"]);
            $code = trim($_GET["code"]);
            !in_array($method, array('mobile', 'email', 'letter')) && $method = 'mobile';

            $field = $method . "cont";
            $this->data['tpl'] = DB::getDB()->selectrow("message_set", "text,$field", "code='$code'"); //如果模版不存在
            $this->data['code'] = $code;
            $this->data['field'] = $field;
            $this->data['method'] = $method;

            $this->data['leftcur'] = "message_tplset";
            $this->output("messagetpl_oper");
        }
    }

}
