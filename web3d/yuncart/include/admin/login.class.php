<?php

defined('IN_CART') or die;

/**
 *  
 * 
 *  登录
 *
 */
class Login extends Base
{

    /**
     *  
     * 
     *  登录
     *
     */
    public function index()
    {
        if (ispostreq()) {
            //用户名，密码
            $uname = trim($_POST['uname']);
            $pass = trim($_POST['pass']);

            //验证码
            $seccode = strtolower(trim($_POST["seccode"]));
            $sess_verify = '';
            if (isset($_SESSION['verify'])) {
                $sess_verify = strtolower($_SESSION['verify']);
                //销毁verify
                unset($_SESSION['verify']);
            }

            if (!$seccode || ($sess_verify != $seccode)) {//如果验证码不存在
                $this->data["error"] = __('wrong_seccode');
            } else {
                //查询
                $admin = DB::getDB()->selectrow("admin", "adminid,uname,pass,salt,role,issuper", "uname='" . $uname . "'");

                if (!empty($admin) && checkpass($pass, $admin['salt'], $admin['pass'])) {

                    //记录cookie
                    if (isset($_POST["remember"])) {
                        csetcookie("adminid", $admin['adminid'], 31536000);
                        csetcookie("auth", md5($admin['salt'] . $admin['pass']), 31536000);
                    }
                    unset($admin['salt'], $admin['pass']);

                    $_SESSION['admin'] = $admin;
                    $_SESSION['admin']['token'] = md5(mt_rand());

                    //更新用户最后登录时间
                    DB::getDB()->update("admin", array("lasttime" => time()), "adminid=" . $admin["adminid"]);

                    //权限
                    $_SESSION['admin']['privs'] = array();
                    if (!$admin['issuper'] && $admin['role']) {//不是超管
                        $tmppriv = DB::getDB()->selectcol("role", "privilege", "roleid in (" . $admin['role'] . ")");
                        if ($tmppriv) {
                            foreach ($tmppriv as $val) {
                                $_SESSION['admin']['privs'] = array_merge($_SESSION['admin']['privs'], explode(",", $val));
                            }
                        }
                    }
                    $this->adminlog('al_login');
                    redirect(url('admin', 'dashboard'));
                } else {
                    $this->data['error'] = __("user_pass_error");
                }
            }
        } else {
            $this->error();
        }
        $this->output("login");
    }

    /**
     *  
     * 
     *  退出，注销session，清空cookie
     *
     */
    public function logout()
    {
        $this->adminlog('al_logout');
        unset($_SESSION["admin"]);
        csetcookie("adminid", '', 1);
        csetcookie('auth', '', 1);
        redirect(url('admin', 'login'));
    }

    /**
     *  
     * 
     *  管理员找回密码
     *
     */
    public function getpwd()
    {
        if (ispostreq()) {
            $email = trim($_POST["email"]);
            $uname = trim($_POST["uname"]);
            $user = DB::getDB()->selectrow("admin", "adminid", "email='$email' AND uname='$uname'");
            if (!$user) {
                $this->data['error'] = __("admin_is_not_exist");
            } else {
                //加载发送邮件
                require COMMONPATH . "/send.class.php";
                $sendmail = new SendEmail();

                //发送邮件
                $pass = getRandString(8, true);
                $subject = __("getpwd");
                $content = __("pwd_reset_to", $pass);
                $ret = $sendmail->send($email, $subject, $content);
                if ($ret) {//如果发送成功
                    $passarr = encpass($pass);
                    DB::getDB()->update("admin", "pass='" . $passarr['pass'] . "',salt='" . $passarr['salt'] . "'", "adminid='" . $user['adminid'] . "'");
                    $this->data['hint'] = __("getpwd_email_is_send");
                } else {
                    $this->data["error"] = __("email_cannt_send");
                }
            }
        }
        $this->output("getpwd");
    }

}
