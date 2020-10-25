<?php
//require_once dirname(__FILE__) . '/saetv2.ex.class.php';
class douban_oauth
{
    private $_need_request = array('code');

    public function __construct($setting) {
        $this->redirect_uri = U('oauth/callback', array('mod'=>'douban'), '', '', true);
        $this->setting = $setting;
    }
    public function getAuthorizeURL() {
        $oauth = new SaeTOAuthV2($this->setting['app_key'], $this->setting['app_secret']);
        return $oauth->getAuthorizeURL($this->redirect_uri);
    }
     
    
}