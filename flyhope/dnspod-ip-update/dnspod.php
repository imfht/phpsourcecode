<?php
/**
 * DNSPOD接口操作
 *
 * @package Model
 * @link https://www.dnspod.cn/docs/index.html
 * @author chengxuan <i@chengxuan.li>
 */
namespace Dnspod;
class Api {
    
    /**
     * API根据路径
     * 
     * @var unknown
     */
    protected $_api_base = 'https://dnsapi.cn/';
    
    /**
     * 登录TOKEN
     * 
     * @var string
     */
    protected $_login_token = '';
    
    /**
     * 构造方法，传入默认Token
     * 
     * @param string $login_token
     */
    public function __construct($login_token) {
        $this->_login_token = $login_token;
    }
    
    /**
     * POST提交一个请求
     * 
     * @param string $uri
     * @param array  $post_data
     * 
     * @return \stdClass
     */
    public function post($uri, array $post_data) {
        $url = $this->_api_base . $uri;
        $post_data['login_token'] = $this->_login_token;
        $post_data['lang'] = 'en';
        $post_data['format'] = 'json';
        $post_data_str = http_build_query($post_data);

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => $post_data_str,
            CURLOPT_TIMEOUT    => 10,
            CURLOPT_USERAGENT  => 'Local ip update /1.0.0 (msg@chengxuan.li)',
        ));
        
        if(defined('CURLOPT_SSL_VERIFYPEER')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        
        $result = curl_exec($ch);

        if($result === false) {
            throw new Exception_Request(curl_error($ch), curl_errno($ch));
        }
        curl_close($ch);
        $result = json_decode($result);
        
        //异常数据
        if(empty($result->status->code) || $result->status->code != '1') {
            throw new Exception($result->status->message, $result->status->code);
        }
        
        
        return $result;
    }
    
    /**
     * 获取域名信息
     * 
     * @param string $domain_id_or_domain
     * 
     * @return \stdClass
     */
    public function domainInfo($domain_id_or_domain) {
        $post_data = array();
        if(ctype_digit($domain_id_or_domain)) {
            $post_data['domain_id'] = $domain_id_or_domain;
        } else {
            $post_data['domain'] = $domain_id_or_domain;
        }
        
        return $this->post('Domain.Info', $post_data);
    }
    
    /**
     * 获取记录列表
     * 
     * @param int    $domain_id
     * @param string $sub_domain
     * 
     * @return \stdClass
     */
    public function recordList($domain_id, $sub_domain = null) {
        $post_data = array(
            'domain_id' => $domain_id,
        );
        
        $sub_domain && $post_data['sub_domain'] = $sub_domain;
        return $this->post('Record.List', $post_data);
    }
    
    /**
     * 添加一条记录
     * 
     * @param int    $domain_id   域名ID
     * @param string $sub_domain  子域名
     * @param string $record_type 域名类型
     * @param string $value       域名值
     * @param int    $ttl         TTL
     * @param string $record_line 域名线路
     * 
     * @return \stdClass
     */
    public function recordCreate($domain_id, $sub_domain, $record_type, $value, $ttl = false, $record_line = '默认') {
        $post_data = array(
            'domain_id'   => $domain_id,
            'sub_domain'  => $sub_domain,
            'record_type' => $record_type,
            'record_line' => $record_line,
            'value'       => $value,
        );
        $ttl && is_numeric($ttl) && $post_data['ttl'] = $ttl;
        return $this->post('Record.Create', $post_data);
    }
    
    /**
     * 修改一条记录
     *
     * @param int    $domain_id   域名ID
     * @param string $sub_domain  子域名
     * @param int    $record_id   记录ID
     * @param string $record_type 域名类型
     * @param string $value       域名值
     * @param int    $ttl         TTL
     * @param string $record_line 域名线路
     *
     * @return \stdClass
     */
    public function recordModify($domain_id, $sub_domain, $record_id, $record_type, $value, $ttl = false, $record_line = '默认') {
        $post_data = array(
            'domain_id'   => $domain_id,
            'record_id'   => $record_id,
            'sub_domain'  => $sub_domain,
            'record_type' => $record_type,
            'record_line' => $record_line,
            'value'       => $value,
        );
        $ttl && is_numeric($ttl) && $post_data['ttl'] = $ttl;
        return $this->post('Record.Modify', $post_data);
    }
    
    /**
     * 删除一条记录
     * 
     * @param int $domain_id 域名ID
     * @param int $record_id 记录ID
     * 
     * @return \stdClass
     */
    public function recordRemove($domain_id, $record_id) {
        return $this->post('Record.Remove', array(
            'domain_id' => $domain_id,
            'record_id' => $record_id,
        ));
    }
    
    /**
     * 动态更新域名记录
     * 
     * @param int    $domain_id   域名ID
     * @param int    $record_id   记录ID
     * @param string $sub_domain  子域名
     * @param string $value       指定IP，不提交则自动获取
     * @param string $record_line 线路
     * 
     * @return \stdClass
     */
    public function recordDdns($domain_id, $record_id, $sub_domain, $value = null, $record_line = '默认') {
        $post_data = array(
            'domain_id'   => $domain_id,
            'record_id'   => $record_id,
            'sub_domain'  => $sub_domain,
            'record_line' => $record_line,
        );
        $value && $post_data['value'] = $value;
        return $this->post('Record.Ddns', $post_data);
    }
}


//异常
class Exception_Request extends \Exception {}
class Exception extends \Exception {}