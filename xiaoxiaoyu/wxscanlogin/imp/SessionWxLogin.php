<?php
require_once 'IWxLogin.php';
/**
 * Description of SessionWxLogin
 *
 * @author Hades
 */
class SessionWxLogin implements IWxLogin{
    private $isSessionInit=false;
    public function getNewToken() {
        $this->initSession();
        //需要重新初始化session
        session_regenerate_id();
        $sessid=  session_id();
        $_SESSION["alive"]=1;
        return $sessid;
    }

    public function getUserInfo($token="") {
        if(strlen($token)>0){
                  session_id($token);
        }
  
        $this->initSession();
        if(!isset($_SESSION["userinfo"])){
            return null;
        }else{
            return $_SESSION["userinfo"];
        }
    }

    public function isExprise($token) {
        session_id($token);
        $this->initSession();
        
        return !isset($_SESSION["alive"]);
    }
    
    public function isLogined($token) {
        $userinfo=  $this->getUserInfo($token);
        return !is_null($userinfo);
    }
    
    //因为之后的获取指定的sessionid，所以不在__construct初始化session
    public function initSession(){
        if($this->isSessionInit) return ;
        session_start();
        $this->isSessionInit=true;
    }

    public function setUserInfo($token, $userinfo) {
        session_id($token);
        $this->initSession();
        $_SESSION["userinfo"]=$userinfo;
    }

}
