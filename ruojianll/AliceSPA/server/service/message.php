<?php


class Message{
    private $user_id;
    private  $account;
    private $password;
    function  __construct($user_id,$account,$password){
        $this->user_id = $user_id;
        $this->account = $account;
        $this->password = $password;
    }
    function send($content,$mobile){
        $gateway = 'http://xtx.telhk.cn:8888/sms.aspx?action=send&userid='.$this->user_id.'&account='.$this->account.'&password='.$this->password.'&mobile='.$mobile.'&content='.urlencode($content).'&sendTime=';
        return simplexml_load_string(file_get_contents($gateway));
    }
    function  overage(){
        $gateway = 'http://xtx.telhk.cn:8888/sms.aspx?action=overage&userid='.$this->user_id.'&account='.$this->account.'&password='.$this->password;
        return simplexml_load_string(file_get_contents($gateway));
    }

}