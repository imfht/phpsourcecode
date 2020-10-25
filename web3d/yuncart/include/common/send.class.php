<?php

defined('IN_CART') or die;

/**
 *
 * 发送类
 * 
 */
class Send
{

    protected $pernum = 1; //每次发送几条记录
    protected $type = ""; //发送的类型 email或sms

    /**
     *
     * 构造函数，确定发送的类型和数量
     * 
     */

    public function __construct($type, $pernum = 1)
    {
        if ($pernum > 10 || $pernum < 1)
            $pernum = 1;
        $this->pernum = $pernum;
        $this->type = $type;
    }

    /**
     *
     * 执行发送操作
     * 
     */
    public function dosend()
    {
        //获取需要发送的记录
        $jpara = array("on" => "contentid");
        $fields = array("a" => "contentid,contentid,notify,addtime", "b" => "subject,content");
        $orderby = array("a" => "contentid");
        $where = array("a" => "type='" . $this->type . "'");
        $queuelist = DB::getDB()->join("notify_queue", "notify_content", $jpara, $fields, $where, $orderby, $this->pernum);
        if (!$queuelist)
            return;


        //确定发送类
        $class = new stdClass();
        if ($this->type == "email") {
            $class = new SendEmail();
        } elseif ($this->type == "sms") {
            $class = new SendSms();
        }
        //循环发送
        $adddata = $contentids = array();
        $time = time();
        foreach ($queuelist as $queue) {
            if ($this->type == 'email') {
                $ret = $class->send($queue['notify'], $queue['subject'], $queue['content']);
            } elseif ($this->type == "sms") {
                $ret = $class->send($queue['notify'], $queue['content']);
            }
            if (!$ret) {
                $msg = $class->getError();
                DB::getDB()->update("notify_queue", "msg='$msg'", "contentid='" . $queue['contentid'] . "'");
            } else {
                $contentids[] = $queue['contentid'];
                $adddata[] = array("addtime" => $time, "type" => $this->type, "contentid" => $queue['contentid'], "receiver" => $queue['notify']);
            }
        }

//		//删除数据记录
        if ($contentids) {
            DB::getDB()->delete("notify_queue", "contentid in " . cimplode($contentids));
            DB::getDB()->updatebool("notify_content", "issend", "contentid in " . cimplode($contentids));
            DB::getDB()->insertMulti("notify_sender", $adddata);
        }
    }

}

/**
 *
 * 发送Email类
 * 
 */
class SendEmail
{

    var $fp;
    var $auth; //是否需要认证
    var $host; //smtp主机
    var $port; //smtp端口
    var $user; //smtp用户
    var $pass; //smtp密码
    var $error = ''; //smtp错误

    /**
     *
     * 构造函数，确定smtp信息
     * 
     */

    public function __construct($host = '', $port = '', $user = '', $pass = '')
    {
        $this->host = $host ? $host : getConfig('smtphost');
        $this->port = $port ? $port : getConfig('smtpport');
        $this->user = $user ? $user : getConfig('smtpuser');
        $this->pass = $pass ? $pass : getConfig('smtppass');
        $this->charset = 'utf-8';
    }

    /**
     *
     * 发送前验证
     * 
     */
    private function presend($toemail)
    {
        $res = $this->getData();
        if ($this->sub($res) != '220') {
            $this->error($res);
            return false;
        }

        fputs($this->fp, ($this->user ? "EHLO" : "HELO") . " " . $this->host . CRLF);
        $res = $this->getData();
        if ($this->sub($res) != '250') {
            $this->error($res);
            return false;
        }

        while (true) {
            $res = $this->getData();
            if (empty($res) || substr($res, 3, 1) != '-') {
                break;
            }
        }

        if ($this->user) {
            $this->sendData("AUTH LOGIN");
            $res = $this->getData();
            if ($this->sub($res) != '334') {
                $this->error($res);
                return false;
            }
            //发送smtp用户
            $this->sendData(base64_encode($this->user));
            $res = $this->getData();
            if ($this->sub($res) != '334') {
                $this->error($res);
                return false;
            }
            //发送smtp密码
            $this->sendData(base64_encode($this->pass));
            $res = $this->getData();
            if ($this->sub($res) != '235') {
                $this->error($res);
                return false;
            }
        }

        //mail from
        $this->sendData("MAIL FROM: <" . $this->user . ">");
        $res = $this->getData();
        if ($this->sub($res) != '250') {
            $this->error($res);
            return false;
        }

        //收件人
        $this->sendData("RCPT TO: <" . $toemail . ">");
        $res = $this->getData();
        if ($this->sub($res) != '250') {
            $this->error($res);
            return false;
        }

        $this->sendData("DATA");
        $res = $this->getData();
        if ($this->sub($res) != '354') {
            $this->error($res);
            return false;
        }
        return true;
    }

    /**
     *
     * 连接
     * 
     */
    private function conn()
    {
        if (!$this->host) {
            $this->error("Not config email");
            return false;
        }
        $this->fp = @fsockopen($this->host, $this->port, $errno, $errstr, 30);
        if (!$this->fp) {
            $this->error("Unable to connect to the SMTP server");
            return false;
        }
        stream_set_blocking($this->fp, true);
        return true;
    }

    /**
     *
     * 发送
     * 
     */
    public function send($toemail, $subject, $content)
    {
        if (!$this->conn())
            return false;
        if (!$this->presend($toemail))
            return false;

        $headerstr = $this->buildHeader($toemail, $subject);
        $this->sendData($headerstr);
        $this->sendData(CRLF);
        $this->sendData(chunk_split(base64_encode($content)) . CRLF . ".");

        $res = $this->getData();
        if ($this->sub($res) != '250') {
            $this->error($res);
            return false;
        }
        $this->sendData("QUIT");
        return true;
    }

    /**
     *
     * header
     * 
     */
    private function buildHeader($toemail, $subject)
    {
        $headers = array();
        $headers[] = 'Date: ' . gmdate("r");
        $headers[] = 'To: "' . '=?' . $this->charset . '?B?' . base64_encode($toemail) . '?=' . '"<' . $toemail . '>';
        $headers[] = 'From: "' . '=?' . $this->charset . '?B?' . base64_encode(getConfig("mallname", "cart")) . '?=' . '" <' . $this->user . '>';
        $headers[] = 'Subject: ' . '=?' . $this->charset . '?B?' . base64_encode($subject) . '?=';
        $contenttype = 'Content-Type: text/html; charset=' . $this->charset;
        $headers[] = $contenttype . '; format=flowed';
        $headers[] = 'Content-Transfer-Encoding: base64';
        return implode(CRLF, $headers);
    }

    /**
     *
     * 从smtp服务器获取信息
     * 
     */
    private function getData($length = 512)
    {
        return fgets($this->fp, $length);
    }

    /**
     *
     * 发送数据到smtp服务器
     * 
     */
    private function sendData($data)
    {
        fputs($this->fp, $data . CRLF);
    }

    /**
     *
     * 截取
     * 
     */
    private function sub($str, $length = 3)
    {
        return substr($str, 0, $length);
    }

    /**
     *
     * 错误
     * 
     */
    private function error($error)
    {
        $this->error = $error;
        clog("smtp", $this->error);
    }

    /**
     *
     * 返回错误
     * 
     */
    public function getError()
    {
        return $this->error;
        //return __("send_email_error_check_conf");
    }

}

class SendSms
{

    var $sms;

    /**
     *
     * 构造函数，确定smtp信息
     * 
     */
    public function __construct()
    {
        $smsphone = getConfig('smsphone');
        $smspass = getConfig('smspass');
        include_once COMMONPATH . "/sms.class.php";
        $this->sms = new Sms($smsphone, $smspass);
    }

    /**
     *
     * 发送
     * 
     */
    public function send($phone, $content)
    {
        return $this->sms->send($phone, $content);
    }

    public function getError()
    {
        return $this->sms->getError();
    }

}
