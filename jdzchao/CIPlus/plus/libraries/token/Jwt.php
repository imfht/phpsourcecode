<?php defined('BASEPATH') or exit ('No direct script access allowed');
include_once CIPLUS_PATH . 'Jwt.abstract.php';

class Jwt extends CIPlus\Jwt {
    protected $CI;

    private $key;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->config->load('token', true, true);
        $config = $this->CI->config->item('token');
        $this->key = $this->CI->config->item('encryption_key');
        parent::__construct($config);
    }

    public function initHeader() {
        return array(
            'typ' => 'jwt',
            'rap' => 'norm'
        );
    }

    public function encrypt($cipher, $rap) {
        $this->CI->load->library('encryption');
        $alg = $this->sheet[$rap];
        $this->CI->encryption->initialize($alg);
        $sign = $this->CI->encryption->encrypt($cipher);
        return $sign;
    }

    public function decrypt($sign, $rap) {
        $this->CI->load->library('encryption');
        $alg = $this->sheet[$rap];
        $this->CI->encryption->initialize($alg);
        $cipher = $this->CI->encryption->decrypt($sign);
        return $cipher;
    }

    public function refresh($header, $payload) {
        // TODO: Implement refresh() method.
    }

}