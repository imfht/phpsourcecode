<?php

/**
 * api的父类控制器,所有api控制器必需继承此控制器
 */
class BaseAPI_Controller extends CI_Controller
{
    protected $time = null;
    protected $now = null;
    protected $user = null;
    protected $mpwx_user = null;
    protected $result = null;

    public function __construct()
    {
        parent::__construct();
        //errcode=供调试用的内部错误代码
        //error_code=对外显示的错误代码
        $this->result = array(
            'errcode' => 'ok',
            'error_code' => 'ok',
        );

        $this->time = time();
        $this->now = date('Y-m-d H:i:s', $this->time);
        if (isset($_SESSION['user'])) {
            $this->user = $_SESSION['user'];
        }
        //微信公众平台
        if (isset($_SESSION['mpwx_user'])) {
            $this->mpwx_user = $_SESSION['mpwx_user'];
        }
    }

    /**
     * 析构函数，在此输出接口返回内容
     */
    public function __destruct()
    {
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * 使得带有参数的url省略默认index方法可以正常访问,如直接访问task/13而不需要task/index/13
     */
    public function _remap($method, $params = array())
    {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        } else {
            //如果没有方法，则把$method压入第一个参数
            $arr1stParam = array($method);
            $params = array_merge($arr1stParam, $params);
            call_user_func_array(array($this, 'index'), $params);
        }
    }
}
