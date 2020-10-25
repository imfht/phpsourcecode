<?php

/**
 *
 * 发送消息通知
 *
 */
class MQ
{

    private $code = '';
    private $search = array(
        "tradepay" => array("{tradeid}", "{tradetotal}"),
        "tradecreated" => array("{tradeid}", "{tradetotal}"),
        "tradeclose" => array("{tradeid}", "{tradetotal}"),
        "tradesend" => array("{tradeid}", "{sendcom}", "{sendno}"),
        "nostock" => array("{uname}", "{email}", "{itemname}", "{price}", "{itemurl}"),
        "downprice" => array("{uname}", "{email}", "{itemname}", "{price}", "{itemurl}")
    );

    public function __construct($code = '')
    {
        $this->setCode($code);
    }

    public function setCode($code)
    {
        $this->code = $code;
        $this->message_set = DB::getDB()->selectrow("message_set", "*", "code='$code'");
    }

    /**
     *
     * 发送
     *
     */
    public function send($uid, $config = array())
    {
        if (!$this->message_set['mobile'] && !$this->message_set['email'] && !$this->message_set['letter'])
            return false;
        //获取用户信息
        $user = DB::getDB()->selectrow("user", "link,email,uname", "uid='$uid'");
        if (!$user)
            return false;

        if ($this->message_set['mobile']) {//如果需要发送短信，必须知道手机号码
            $mobile = (isset($config["mobile"]) && $config["mobile"]) ? $config["mobile"] : $user['link'];
            if ($mobile) {
                $content = str_replace($this->search[$this->code], $config["replacement"], $this->message_set["mobilecont"]);
                $this->smsqueue($mobile, $content);
            }
        }
        if ($this->message_set['email']) {//如果需要邮件,必须知道邮件地址
            $email = (isset($config["email"]) && $config["email"]) ? $config["email"] : $user["email"];
            if ($email) {
                $title = isset($config["emailtitle"]) ? $config["emailtitle"] : __($this->code);
                $content = str_replace($this->search[$this->code], $config["replacement"], $this->message_set["emailcont"]);
                $this->mailqueue($email, $title, $content);
            }
        }
        if ($this->message_set["letter"]) {
            $uname = (isset($config["uname"]) && $config["uname"]) ? $config["uname"] : $user["uname"];
            if ($uname) {
                $title = isset($config["lettertitle"]) ? $config["lettertitle"] : __($this->code);
                $content = str_replace($this->search[$this->code], $config["replacement"], $this->message_set['lettercont']);
                $this->sendletter($uid, $uname, $title, $content);
            }
        }
    }

    /**
     *
     * 发送email
     *
     */
    public function mailqueue($email, $subject = "", $content = "")
    {
        //insert一条发送email记录
        $contentid = DB::getDB()->insert("notify_content", array("subject" => $subject, "content" => $content, "type" => "email"));
        DB::getDB()->insert("notify_queue", array("notify" => $email, "type" => "email", "addtime" => time(), "contentid" => $contentid));
        return true;
    }

    /**
     *
     * 发送短信
     *
     */
    public function smsqueue($mobile, $content = "")
    {
        //insert一条发送手机记录
        $contentid = DB::getDB()->insert("notify_content", array("content" => $content, "type" => "sms"));
        DB::getDB()->insert("notify_queue", array("notify" => $mobile, "type" => "sms", "addtime" => time(), "contentid" => $contentid));
        return true;
    }

    /**
     *
     * 发送站内信
     *
     */
    public function sendletter($uid, $uname = '', $subject = "", $content = "")
    {
        DB::getDB()->insert("letter", array("uid" => $uid, "uname" => $uname, "subject" => $subject, "content" => $content, "addtime" => time()));
        //更新用户未读数
        DB::getDB()->updatecre("user", "unread", "uid='$uid'", true);
        return true;
    }

}
