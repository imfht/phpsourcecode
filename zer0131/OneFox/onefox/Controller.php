<?php
/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 抽象控制器
 */

namespace onefox;

abstract class Controller {

    const CODE_SUCCESS = 0;
    const CODE_FAIL = 1;

    public $actions = [];

    public function __construct() {
        //此方法可初始化控制器
        if (method_exists($this, '_init')) {
            $this->_init();
        }
    }

    // 模板赋值
    protected function assign($name, $val = '') {
        View::singleton()->assign($name, $val);
    }

    // 模板输出
    protected function show($tpl = '') {
        View::singleton()->render($tpl);
    }

    // 引入模板
    protected function import($path, $val = []) {
        View::singleton()->import($path, $val);
    }

    // 获取模板数据流
    protected function fetch($tpl = '') {
        return View::singleton()->fetch($tpl);
    }

    /**
     * json输出
     * @param int $errno 状态码
     * @param string $errmsg 返回信息
     * @param null|array $data 返回数据
     * @param null|array $ext 返回数据扩展信息
     */
    protected function json($errno, $errmsg, $data = null, $ext = null) {
        $res = [
            'errno' => $errno,
            'errmsg' => $errmsg,
            'data' => $data
        ];
        if ($ext && is_array($ext)) {
            foreach ($ext as $k => $v) {
                $res[$k] = $v;
            }
        }
        Response::json($res);
    }

    /**
     * 获取GET请求参数
     * @param $key
     * @param string $default
     * @param string $type
     * @return string
     */
    protected function get($key, $default = '', $type = 'str') {
        return Request::get($key, $default, $type);
    }

    /**
     * 获取POST请求参数
     * @param $key
     * @param string $default
     * @param string $type
     * @return string
     */
    protected function post($key, $default = '', $type = 'str') {
        return Request::post($key, $default, $type);
    }

    /**
     * 获取PUT请求参数
     * @param $key
     * @param string $default
     * @param string $type
     * @return string
     */
    protected function put($key, $default = '', $type = 'str') {
        if (Request::method() !== 'put') {
            return $default;
        }
        $json = Request::stream();
        $data = json_decode($json, true);
        if (!$data) {
            return $default;
        }
        $data = Request::filterArray($data);
        return Request::filter($key, $data, $default, $type);
    }

    /**
     * 获取DELETE请求参数
     * @param $key
     * @param string $default
     * @param string $type
     * @return string
     */
    protected function delete($key, $default = '', $type = 'str') {
        if (Request::method() !== 'delete') {
            return $default;
        }
        $json = Request::stream();
        $data = json_decode($json, true);
        if (!$data) {
            return $default;
        }
        $data = Request::filterArray($data);
        return Request::filter($key, $data, $default, $type);
    }

    /**
     * 获取PATCH请求参数
     * @param $key
     * @param string $default
     * @param string $type
     * @return string
     */
    protected function patch($key, $default = '', $type = 'str') {
        if (Request::method() !== 'patch') {
            return $default;
        }
        $json = Request::stream();
        $data = json_decode($json, true);
        if (!$data) {
            return $default;
        }
        $data = Request::filterArray($data);
        return Request::filter($key, $data, $default, $type);
    }
}