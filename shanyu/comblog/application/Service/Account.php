<?php
namespace App\Service;

use Kernel\Loader;
use Kernel\config;
use App\Model\Admin as AccountModel;

class Account
{
    protected $single_login;
    protected $long_login;

    protected $session_id;
    protected $cookie_token;
    protected $cookie_expire;
    public function __construct()
    {
        if(session_status() != 2) session_start();
        $this->session_id = session_id();

        $this->single_login = true;//单人登录
        $this->long_login = true;//持久登录
    }


    public function isLogin()
    {
        $account_id = $this->getLoginId();
        if($account_id) return $account_id;

        if($this->long_login){
            $cookie_token = $this->getCookieToken();
            if($cookie_token){
                $account = Loader::singleton(AccountModel::class)->getDataByCookieToken($cookie_token);
                $this->setLoginId($account['id']);
                return intval($account['id']);
            }
        }

        return 0;
    }


    public function setLoginId($account_id)
    {
        $_SESSION['login_id']=intval($account_id);
        return $this->session_id;
    }
    public function getLoginId()
    {
        return isset($_SESSION['login_id']) ? intval($_SESSION['login_id']) : 0;
    }
    public function delLoginId($session_id='')
    {
        session_unset();
        session_destroy();
    }


    public function removeLogin($session_id,$update_time)
    {
        if(empty($session_id)) return true;
        if(!$this->single_login) return true;

        $session_time = strtotime($update_time);
        $session_lifetime = ini_get("session.gc_maxlifetime");
        if(time() > $session_time + $session_lifetime) return true;
        
        session_id($session_id);
        $status = session_destroy();
        if(session_status() != 2) session_start();
        return $status;
    }

    public function cookieToken()
    {
        return md5(Config::instance()->get('encrypt').$this->session_id);
    }
    public function setCookieToken()
    {
        $this->cookie_token = $this->cookieToken();
        $this->cookie_expire = time() + 7*24*60*60;
        return setcookie('cookie_token', $this->cookie_token, $this->cookie_expire,'/', '', false);
    }
    public function getCookieToken()
    {
        if(!isset($_COOKIE['cookie_token']) || !preg_match("/^[a-z0-9]{32}$/", $_COOKIE['cookie_token'])) return '';
        return htmlspecialchars($_COOKIE['cookie_token']);
    }
    public function delCookieToken()
    {
        return setcookie('cookie_token', '', time() - 3600);
    }


    public function getLoginData()
    {
        $vars = [];
        if($this->session_id) $vars['session_id'] = $this->session_id;
        if($this->cookie_token) $vars['cookie_token'] = $this->cookie_token;
        if($this->cookie_expire) $vars['cookie_expire'] = $this->cookie_expire;
        return $vars;
    }


}