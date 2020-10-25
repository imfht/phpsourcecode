<?php

defined('IN_CART') or die;

/**
 *  
 * 用户
 *
 *
 * */
class User extends Base
{

    /**
     *  
     * 用户登陆
     *
     *
     * */
    public function login()
    {
        $isajax = !empty($_GET["ajax"]) ? 1 : 0;
        $this->data["opertype"] = "login";
        $this->data['tlogins'] = $this->getTlogin();
        $template = $isajax ? "ajaxloginreg" : "login";
        if (!$isajax && !empty($_SESSION["uid"])) {//如果用户已经登录，跳转到用户控制面板
            redirect(url("index", 'member'));
        }
        $this->output($template);
    }

    private function getTlogin()
    {
        return DB::getDB()->select("tlogin", "*", "ispublish=1", "order");
    }

    public function isLogined()
    {
        exit(!empty($_SESSION['uid']) ? "true" : "false");
    }

    /**
     *  
     * 用户注册
     *
     *
     * */
    public function reg()
    {
        $this->getHint();
        $isajax = !empty($_GET["ajax"]) ? 1 : 0;
        $template = $isajax ? "ajaxloginreg" : "reg";
        if (!$isajax && !empty($_SESSION["uid"])) {//如果用户已经登录，跳转到用户控制面板
            redirect(url("index", 'member'));
        }
        $this->data["opertype"] = "reg";
        $this->output($template);
    }

    /**
     *  
     * 检查用户名
     *
     *
     * */
    public function checkname()
    {
        $uname = trim($_POST["uname"]);
        $isexist = false;
        if ($uname) {
            $isexist = DB::getDB()->selectexist("user", "uname='" . $uname . "'");
        }
        exit($isexist ? "failure" : "success");
    }

    /**
     *  
     * 保存注册用户
     *
     *
     * */
    public function doreg()
    {
        //接收条件
        $uname = trim($_POST["uname"]);
        $pass = $_POST["pass"];
        $pass2 = $_POST["pass2"];
        $email = trim($_POST["email"]);
        $referer = empty($_SERVER["HTTP_REFERER"]) ? url('index', 'index') : $_SERVER['HTTP_REFERER'];

        //判断验证码
        $seccode = strtolower(trim($_POST["seccode"]));
        $sess_verify = '';
        if (isset($_SESSION['verify'])) {
            $sess_verify = strtolower($_SESSION['verify']);
            //销毁verify
            unset($_SESSION['verify']);
        }

        if (!$seccode || ($sess_verify != $seccode)) {
            $this->setHint("wrong_seccode", "error", url("index", "user", 'reg'));
        }

        //判断用户
        if (DB::getDB()->selectexist("user", "uname='" . $uname . "'")) {
            $this->setHint("uname_exist", "error", url("index", "user", 'reg'));
        }

        //密码
        $len = strlen($pass);
        if ($len < 4 || $len > 20) {
            $this->setHint("pass_length_error", "error", url("index", "user", 'reg'));
        }
        if ($pass != $pass2) {
            $this->setHint("pass_not_equal", "error", url("index", "user", 'reg'));
        }
        if (!$email || !isemail($email)) {
            $this->setHint("email_error", "error", url("index", "user", 'reg'));
        }

        //入库
        $data = array("uname" => $uname, "email" => $email);
        $data += encpass($pass);
        $data["regip"] = getClientIp();
        $data["lasttime"] = $data["regtime"] = time();
        $uid = DB::getDB()->insert("user", $data);
        unset($pass, $data);

        //session;
        $_SESSION["uname"] = $uname;
        $_SESSION["uid"] = $uid;

        redirect($refer);
    }

    /**
     *  
     * 退出
     *
     *
     * */
    public function logout()
    {
        $referer = $_SERVER["HTTP_REFERER"];
        if ($_SESSION["uid"]) {
            //判断用户的类型
            $uid = intval($_SESSION['uid']);
            $source = DB::getDB()->selectval("user", "source", "uid='$uid'");
            if ($source)
                unset($_SESSION['oauth']);
            unset($_SESSION['uid'], $_SESSION['uname']);
        }
        $refer = $_SERVER["HTTP_REFERER"];
        redirect($refer);
    }

    /**
     *  
     * 登陆
     *
     *
     * */
    public function dologin()
    {
        $uname = trim($_POST["uname"]);
        $pass = $_POST["pass"];
        if ($uname && $pass) {
            $user = DB::getDB()->selectrow("user", "uid,uname,pass,salt,lastpost", "uname='" . $uname . "'");
            if (!empty($user) && checkpass($pass, $user["salt"], $user["pass"])) {//判断登陆
                $_SESSION["uname"] = $uname;
                $_SESSION["uid"] = $user["uid"];
                $_SESSION['lastpost'] = $user['lastpost'];
                DB::getDB()->update("user", "lasttime=" . time(), "uid='{$user['uid']}'");
                $url = (!empty($_SERVER["HTTP_REFERER"]) && !preg_match("/model=user/i", $_SERVER['HTTP_REFERER'])) ? $_SERVER["HTTP_REFERER"] : url('index', 'member', 'index');
                exit(json_encode(array("ret" => "success", "msg" => $url, "uname" => $uname)));
            }
        }
        exit(json_encode(array("ret" => "failure", "msg" => __("user_pass_error"))));
    }

    /**
     *  
     * 忘记密码
     *
     *
     * */
    public function forgetpwd()
    {
        if (ispostreq()) {//接受email，发送
            $email = trim($_POST["email"]);
            if ($email && ($user = DB::getDB()->selectrow("user", "uid,uname", "email='$email'"))) {
                //加载发送邮件
                require COMMONPATH . "/send.class.php";
                $sendmail = new SendEmail();

                //邮件
                $time = time();
                $subject = __("getpwd");
                $content = file_get_contents(DATADIR . "/filemsg/forgetpwd.html");
                $search = array("{uname}", "{forgetpwd}");
                $mailkey = substr(md5($email . $time), 8, 8);
                $url = getConfig('weburl') . url('index', 'user', 'setpwd', 'uid=' . $user["uid"] . '&mailkey=' . $mailkey);
                $replacement = array($user['uname'], $url);
                $content = str_replace($search, $replacement, $content);

                //发送
                $ret = $sendmail->send($email, $subject, $content);
                if ($ret) {//如果发送成功
                    DB::getDB()->update("user", array("mailkey" => $mailkey, "mailtime" => $time), "uid='" . $user["uid"] . "'");
                }
                $this->setHint("forgetpwd_send_" . ($ret ? "success" : "failure"), $ret ? "success" : "error");
            } else {
                $this->setHint("email_not_exist", "error");
            }
        } else {
            $this->getHint();
            $this->output("forgetpwd");
        }
    }

    public function setpwd()
    {
        if (ispostreq()) {//设置新密码
            $pass = $_POST["pass"];
            $pass2 = $_POST["pass2"];
            $uid = $_POST["uid"];
            $mailkey = $_POST["mailkey"];
            //判断uid和mailkey是否合法
            $user = DB::getDB()->selectrow("user", "mailkey,mailtime", "uid='$uid'");
            if (!$user || ($user['mailkey'] != $mailkey) || (time() - $user['mailtime'] > 86400 )) {//地址不合法
                $this->setHint("forgetpwd_url_error", "error");
            }

            //判断密码是否合法
            $len = strlen($pass);
            if ($len < 4 || $len > 20) {
                $this->setHint("pass_length_error", "error");
            }
            if ($pass != $pass2) {
                $this->setHint("pass_not_equal", "error");
            }

            //执行更新操作
            $data = array("mailkey" => '', "mailtime" => 0);
            $data += encpass($pass);
            DB::getDB()->update("user", $data, "uid='$uid'");
            $this->setHint("pass_set_success", "success");
        } else {
            $this->getHint();
            $this->data['uid'] = intval($_GET["uid"]);
            $this->data['mailkey'] = trim($_GET["mailkey"]);
            $this->output("setpwd");
        }
    }

}
