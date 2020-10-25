<?php
/**
 * @author Cigery(bestentech@gmail.com)
 * @license MIT
 */

class alisms
{
    private $_key;
    
    private $_secret;
    
    private $_setting = array();
    
    public $error = array();
    
    public function __construct($key = '', $secret = '')
    {
        $this->_key = $key;
        $this->_secret = $secret;
    }
    
    public function sms_sign($sign = '')
    {
        $this->_setting['sms_free_sign_name'] = $sign;
    }
    
    public function sms_param(array $param = array())
    {
        $this->_setting['sms_param'] = json_encode($param);
    }
    
    public function sms_template($code = '')
    {
        $this->_setting['sms_template_code'] = $code;
    }
    
    public function sms_mobile($mobile = '')
    {
        $this->_setting['rec_num'] = $mobile;
    }
    
    public function send()
    {
        $params = $this->_params();
        $params['sign'] = $this->_signed($params);
        
        $reponse = $this->_curl($params);
        if($reponse !== FALSE)
        {
            $res = json_decode($reponse, TRUE);
            $res = array_pop($res);
            if(isset($res['result'])) return TRUE;
            $this->error = $res;
        }
        else
        {
            $this->error = array('code' => 0, 'msg' => 'HTTP_RESPONSE_NOT_WELL_FORMED');
        }
        return FALSE;
    }
    
    private function _params()
    {
        return array
        (
            'app_key' => $this->_key,
            'format' => 'json',
            'method' => 'alibaba.aliqin.fc.sms.num.send',
            'v' => '2.0',
            'timestamp' => date('Y-m-d H:i:s'),
            'sign_method' => 'md5',
            'sms_type' => 'normal',
        ) + $this->_setting;
    }
    
    private function _signed($params)
    {
        ksort($params);
        $sign = $this->_secret;
        foreach($params as $k => $v)
        {
            if(is_string($v) && '@' != substr($v, 0, 1)) $sign .= $k.$v;
        }
        $sign .= $this->_secret;
        return strtoupper(md5($sign));
    }
    
    private function _curl($params)
    {
        $uri = 'https://eco.taobao.com/router/rest?'.http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Verydows');
        $reponse = curl_exec($ch);
        curl_close($ch);
        return $reponse;
    }
}
?>
