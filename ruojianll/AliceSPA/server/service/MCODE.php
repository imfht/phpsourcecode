<?php

class MCODE{
    private $phalconSession;
    private $message;
    function  __construct($session,$message){
        $this->phalconSession = $session;
        $this->message = $message;
    }
    function  getCurrentCode(){
        if($this->phalconSession->has('MCODE_code')){
            return $this->phalconSession->get('MCODE_code');
        }
        return null;
    }
    function generateCode(){
        return rand(100000,999999);
    }
    function sendACode($mobile,$span = 60){
        if($this->phalconSession->has('MCODE_span')){
            if(time() - $this->phalconSession->get('MCODE_span') < 60){
                return false;
            }
        }
        $code = $this->generateCode();
        $res = $this->message->send('您的验证码是'.$code.'，感谢使用。【若简臻品】',strval($mobile));
        if($res->returnstatus == "Success"){

            $this->phalconSession->set('MCODE_span',time());
            $this->phalconSession->set('MCODE_code',$code);
            return true;
        }
        return false;
    }
    function isCorrect($code,$clear = false){
        $res = strtolower(strval($this->getCurrentCode())) == strtolower(strval($code));
        if($clear){
            $this->clearCode();
        }
        return $res;
    }

    function  clearCode(){
        if($this->phalconSession->has('MCODE_code')){
            $this->phalconSession->remove('MCODE_code');
        }
    }
    function  clearSpan(){
        if($this->phalconSession->has('MCODE_span')){
            $this->phalconSession->remove('MCODE_span');
        }
    }
}