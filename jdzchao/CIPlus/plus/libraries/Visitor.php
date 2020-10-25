<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once CIPLUS_PATH . 'CIClass.abstract.php';

/**
 * 访客类
 * 检测访问用户的基本信息
 * Class Visitor
 */
class Visitor extends \CIPlus\CIClass {
    
    private $env; // 访问环境
    private $ua; // user agent
    
    public function __construct() {
        parent::__construct();
        $this->userAgent();
        $this->environment();
    }
    
    // 获取user agent
    public function userAgent() {
        $this->CI->load->library('user_agent');
        $this->ua = $this->CI->agent->agent_string();
        return $this->ua;
    }
    
    // 访问环境检测
    public function environment() {
        if (strpos($this->ua, 'MicroMessenger') !== false) {
            $this->env = "wechat";
            return $this->env;
        }
        return "browser";
    }
    
    // 获取用户 ip
    public function ip() {
        $this->CI->load->helper('IP');
        return client_ip();
    }
    
    // 获取来访域名
    public function domain() {
        return $_SERVER['HTTP_HOST'];
    }
    
    // 获取设备信息
    public function device() {
        return $this->CI->input->post_get('device');
    }
}