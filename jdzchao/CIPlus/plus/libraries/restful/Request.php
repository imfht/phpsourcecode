<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/CIClass.abstract.php';

Class Request extends \CIPlus\CIClass {

    // 接口参数
    private $_params = array(); // 参数集合
    private $_payload = array(); // 载荷
    private $_roles = array(); // 角色

    protected $token = null;
    protected $token_key;
    protected $token_source;

    public function __construct(array $config = array()) {
        parent::__construct();
        $this->loadConf('restful');
    }

    /**
     * 获取接口参数
     * @param array $required
     * @param array $optional
     * @param string $method
     * @return bool
     */
    public function init($required = array(), $optional = array(), $method = 'request') {
        $method = '_' . $method;
        $params = $this->$method();
        foreach ($required as $key) {
            if (!array_key_exists($key, $params)) {
                return false;
            }
        }
        foreach ($optional as $key) {
            if (!array_key_exists($key, $params)) {
                $params[$key] = null;
            }
        }
        $this->_params = $params;
        return true;
    }

    /**
     * 返回请求参数
     * @param $key
     * @return array|mixed
     */
    public function params($key = '') {
        if (empty($key)) {
            return $this->_params;
        } elseif (array_key_exists($key, $this->_params)) {
            return $this->_params[$key];
        } else {
            return null;
        }
    }

    /**
     * 更新接口参数数据
     * @param $key
     * @param $value
     * @return mixed
     */
    public function updateParam($key, $value) {
        $this->_params[$key] = $value;
        return $value;
    }

    public function get_token() {
        switch ($this->token_source) {
            case 'header':
                $key = strtoupper('HTTP_' . $this->token_key);
                if (key_exists($key, $_SERVER)) {
                    $this->token = $_SERVER[$key];
                }
                break;
            default:
                $this->token = null;
        }
        return $this->token;
    }


    // get method
    protected function _get($key = null) {
        return $this->CI->input->get($key);
    }

    // post method
    protected function _post($key = null) {
        return $this->CI->input->post($key);
    }

    // post or get method
    protected function _request($key = null) {
        $data = NULL;
        if (empty($key)) {
            $get = $this->CI->input->get();
            $post = $this->CI->input->post();
            $data = array_merge($get, $post);
        } else {
            $data = $this->CI->input->post_get($key);
        }
        return $data;
    }

    public function set_payload($payload) {
        $this->_payload = $payload;
    }

    public function payload($key = null) {
        if (empty($key)) {
            return $this->_payload;
        } elseif (array_key_exists($key, $this->_payload)) {
            return $this->_payload[$key];
        } else {
            return null;
        }
    }

    public function set_roles($roles) {
        $this->_roles = $roles;
    }

}