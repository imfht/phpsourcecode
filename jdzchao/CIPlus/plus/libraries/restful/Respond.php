<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/CIClass.abstract.php';

Class Respond extends \CIPlus\CIClass {

    const KEY_CODE = 'code';
    const KEY_MESSAGE = 'message';
    const KEY_DATA = 'data';

    // 详见config/restful.php
    protected $strict = true; // 是否打开严格模式，打开后除了接口信息其他输出无效
    protected $cors = false; // 是否开启CORS跨域访问，必须开打严格模式才可以启动
    protected $param_format; // 数据格式参数
    protected $respondFormat = 'json'; // 默认数据格式
    protected $supportedFormats = array(); // 可被支持的数据格式

    // API默认返回数据
    private $code = 40000;
    private $message = 'Access API Failed';
    private $data = array();

    public function __construct(array $config = array()) {
        parent::__construct();
        $this->loadConf('restful');
        $this->CI->load->library('format');
        $this->CI->lang->load('respond');
        $this->message = lang('m40000');
        ob_start();
    }

    /**
     * 输出接口响应数据
     */
    public function output() {
        $this->_format();
        // 清理输出缓冲区
        if ($this->strict) {
            ob_end_clean();
            // 构造允许跨域 header
            if ($this->cors) {
                header("Access-Control-Allow-Origin: *");
                header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
            }
        }
        // 构造回调数据
        $arr = array(
            self::KEY_CODE => $this->code,
            self::KEY_MESSAGE => $this->message,
            self::KEY_DATA => $this->data
        );
        $this->CI->output->set_content_type($this->supportedFormats[$this->respondFormat]);
        $toFormat = 'to_' . $this->respondFormat;
        $this->CI->output->set_output($this->CI->format->factory($arr)->$toFormat());
        $this->CI->output->_display();
        exit;
    }

    /**
     * Set Respond Params
     * param int $code 接口参数
     * param string $message 接口参数
     * param array $data 接口参数
     */
    public function set() {
        // 根据参数类型解析参数
        $args = func_get_args();
        foreach ($args as $arg) {
            if (is_numeric($arg)) {
                $this->setCode($arg);
            } elseif (is_string($arg)) {
                $this->setMessage($arg);
            } elseif (is_array($arg)) {
                $this->setData($arg);
            }
        }
        return $this;
    }

    /**
     * Set Respond Code
     * @param int $code 代码
     * @param bool $sync 是否同步message
     * @return $this
     */
    public function setCode($code, $sync = true) {
        $this->code = $code;
        if ($sync) {
            $this->message = $this->CI->lang->line('m' . $code, FALSE);
        }
        return $this;
    }

    /**
     * Set Respond Message
     * @param string $message
     * @return $this
     */
    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    /**
     * Set Respond Data
     * @param array $data
     * @return $this
     */
    public function setData(array $data = array()) {
        $this->data = $data;
        return $this;
    }

    // 修改API数据格式
    private function _format() {
        $f = strtolower($this->CI->input->get($this->param_format));
        if (!empty($f) && array_key_exists($f, $this->supportedFormats)) {
            $this->respondFormat = $f;
        }
    }


    public function invalidRequest() {
        $this->setCode(40001)->output();
    }

    public function invalidRole() {
        $this->setCode(40002)->output();
    }

    public function invalidToken() {
        $this->setCode(40099)->output();
    }


}