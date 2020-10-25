<?php

/**
 * HTTP Request请求，支持批量处理
 *
 * @author    Chengxuan <i@chengxuan.li>
 * @package   Comm
 */
namespace Comm\Request;
class Single {

    /**
     * CURL资源
     *
     * @var resource
     */
    protected $_ch;
    
    /**
     * 设置头信息
     * 
     * @var array
     */
    protected $_headers = array();
    
    /**
     * CURL配置
     *
     * @var array
     */
    protected $_option = array();

    /**
     * 构造方法
     *
     * @param   string  $url        URL
     * @param   array   $post_data  提交的数据内容
     *
     * @return  void
     */
    public function __construct($url=null, array $post_data=null) {
        $this->_ch = curl_init();
        $this->_option[CURLOPT_RETURNTRANSFER] = true;
        
        //跳过证书验证
        if(defined('CURLOPT_SSL_VERIFYPEER')) {
            $this->_option[CURLOPT_SSL_VERIFYPEER] = false;
        }
        if(defined('CURLOPT_SSL_VERIFYPEER')) {
            $this->_option[CURLOPT_SSL_VERIFYPEER] = false;
        }
        
        
        $url !== null && $this->setUrl($url);
        $post_data !== null && $this->setPostData($post_data);
    }
    
    /**
     * 析构方法
     * 
     * @return void
     */
    public function __destruct() {
        //关闭CURL句柄
        curl_close($this->_ch);
    }

    /**
     * 设置请求的URL
     *
     * @param string $url URL
     *
     * @return \Comm\Request\Single
     */
    public function setUrl($url) {
        $this->_option[CURLOPT_URL] = $url;
        return $this;
    }

    /**
     * 设置提交参数
     * @param   mixed                   $post_param     提交参数，字符串或数组
     * @param   boolean                 $build_query    是否自动执行http_build_query
     *
     * @return  \Comm\Request\Single
     */
    public function setPostData($post_param, $build_query=true) {
        if($build_query && is_array($post_param)) {
            $post_param = array_filter($post_param, function($value) {
                return $value !== null;
            });
            $post_param = http_build_query($post_param);
        }
        $this->_option[CURLOPT_POSTFIELDS] = $post_param;
        return $this;
    }

    /**
     * 请求头
     *
     * @param array $headers HTTP头信息
     *
     * @return \Comm\Request\Single
     */
    public function setHeader(array $headers) {
        $this->_headers = $headers;
        return $this;
    }
    
    /**
     * 追加Header信息
     * 
     * @param array $headers
     * 
     * @return \Comm\Request\Single
     */
    public function appendHeader(array $headers) {
        $this->_headers = array_merge($this->_headers, $headers);
        return $this;
    }
    
    /**
     * 设置用户代理
     * 
     * @param string $user_agent 用户代理字符串
     * 
     * @return \Comm\Request\Single
     */
    public function setUserAgent($user_agent) {
        $this->_option[CURLOPT_USERAGENT] = $user_agent;
        return $this;
    }
    
    /**
     * 设置CURL选项
     * 
     * @param int   $key
     * @param mixed $value
     * 
     * @return \Comm\Request\Single
     */
    public function setOption($key, $value) {
        $this->_option[$key] = $value;
        return $this;
    }
    


    /**
     * 设置超时时间
     *
     * @param int $timeout 设置超时时间
     *
     * @return \Comm\Request\Single
     */
    public function setTimeout($timeout) {
        if(defined('CURLOPT_TIMEOUT_MS')) {
            $timeout *= 1000;
            $this->_option[CURLOPT_TIMEOUT_MS] = $timeout;
        } else {
            $this->_option[CURLOPT_TIMEOUT] = ceil($timeout);
        }

        return $this;
    }

    /**
     * 获取CURL资源
     *
     * @return resource
     */
    public function fetchCurl() {
        if($this->_headers) {
            $this->_option[CURLOPT_HTTPHEADER] = $this->_headers;
        }
        curl_setopt_array($this->_ch, $this->_option);
        return $this->_ch;
    }

    /**
     * 获取CLI命令行中的curl命令
     *
     * @return string
     */
    public function fetchCurlCli() {
        $url = addslashes($this->_option[CURLOPT_URL]);
        $result = "curl \"{$url}\"";
        if(isset($this->_option[CURLOPT_COOKIE])) {
            $cookie = addslashes($this->_option[CURLOPT_COOKIE]);
            $result .= " -b \"{$cookie}\"";
        }
        if(isset($this->_option[CURLOPT_USERAGENT])) {
            $user_agent = addslashes($this->_option[CURLOPT_USERAGENT]);
            $result .= " -A \"{$user_agent}\"";
        }
        if(isset($this->_option[CURLOPT_POSTFIELDS])) {
            if(!is_array($this->_option[CURLOPT_POSTFIELDS])) {
                $post = addslashes($this->_option[CURLOPT_POSTFIELDS]);
                $result .= " -d \"{$post}\"";
            }

        }
        if(isset($this->_headers)) {
            foreach($this->_headers as $header) {
                $header = addslashes($header);
                $result .= " -H \"{$header}\"";
            }
        }
        return $result;
    }

    /**
     * 执行CURL请求
     * 
     * @param string $response_header 引用返回响应头信息
     *
     * @return string
     */
    public function exec(& $response_header = null) {
        $numargs = func_num_args();
        if($numargs) {
            curl_setopt($this->_ch, CURLOPT_HEADER, true);
        }
        
        $this->fetchCurl($this->_ch);
        $response = curl_exec($this->_ch);
        
        //执行失败抛出异常
        $curl_info = curl_getinfo($this->_ch);
        if($response === false) {
            $code = curl_errno($this->_ch);
            $message = curl_error($this->_ch);
            $metadata = array(
                'info'             => $curl_info,
                'curl_cli_command' => $this->fetchCurlCli(),
            );
            throw new \Exception\Request($message, $code, $metadata);
        }
        
        
        if($numargs && !empty($curl_info['http_code'])) {
            list($response_header, $result) = explode("\r\n\r\n", $response);
        } else {
            $result = $response;
        }
        unset($response);

        return $result;
    }
    
    /**
     * 获取信息
     * 
     * @return mixed
     */
    public function showInfo() {
        return curl_getinfo($this->_ch);
    }

    /**
     * 加载一个文件上传
     * 
     * @param string $file_path 文件路径
     * 
     * @return mixed
     */
    static public function file($file_path) {
        if(class_exists('\CURLFile', false)) {
            $result = new \CURLFile($file_path);
        } else {
            $result = "@{$file_path}";
        }
        return $result;
    }
    
}
