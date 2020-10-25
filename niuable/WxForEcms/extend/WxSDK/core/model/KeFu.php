<?php
namespace WxSDK\core\model;

class KeFu extends Model
{
    public $kf_account;//账号
    public $nickname;
    public $password;
    
    public function __construct(string $account, string $nickname, $password) {
        $this->kf_account = $account;
        $this->nickname = $nickname;
        $this->password = $password;
    }
}

