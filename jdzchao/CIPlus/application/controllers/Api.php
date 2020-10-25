<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    private $key;
    private $title;
    private $path;
    private $required;
    private $optional;
    private $method;
    private $validated;

    public function __construct() {
        parent::__construct();
        $this->load->add_package_path(FCPATH . 'api' . DIRECTORY_SEPARATOR);
        $this->load->library('restful/request');
        $this->load->library('restful/respond');
    }

    public function _remap($method, $params = array()) {
        if (count($params) > 0) {
            array_unshift($params, $method); // 将CI的method合并进参数数组
            $method = array_pop($params); // 取最后一个参数作为方法名
            $lib_path = implode('/', $params); // 将参数构造位api类路径
            $this->validator($lib_path, $method);
            $this->load->library($lib_path, null, 'api');
            if (is_callable(array($this->api, $method))) {
                $res = $this->api->$method($this->request, $this->respond);
                if ($res instanceof Respond) $res->output();
            }
        } else {
            show_404();
        }
    }

    /**
     * 接口验证器
     * @param $lib_path
     * @param $method
     * @return mixed
     */
    private function validator($lib_path, $method) {
        $this->load->model('api_model');
        $api_path = $lib_path . '/' . $method;
        $api = $this->api_model->by_path($api_path);
        if ($api) {
            foreach ($api as $key => $val) {
                if (property_exists($this, $key)) {
                    $this->$key = $val;
                }
            }
            if ($this->validated) $this->verifyToken();
            return $this->verifyRequest($api);
        } else {
            show_404();
            return false;
        }
    }

    /**
     * 验证Token合法性
     */
    private function verifyToken() {
        $this->load->library('token/jwt');
        $token = $this->request->get_token();
        $token = $this->jwt->validator($token);
        $payload = $token['payload'];
        if ($token && $payload['exp'] > time()) {
            $this->request->set_payload($payload);
            $this->verifyRole($payload['id']);
        } else {
            $this->respond->invalidToken();
        }
    }

    /**
     * 验证登录用户接口权限
     * @param $id
     */
    private function verifyRole($id) {
        $this->load->model("role_model");
        $r = $this->role_model->verify($id, $this->key);
        if (!$r) {
            $this->respond->invalidRole();
        }
    }

    /**
     * 验证接口有效参数
     * @param $api
     */
    private function verifyRequest($api) {
        $req = json_decode($api['required'], true);
        $opt = json_decode($api['optional'], true);
        $v = $this->request->init($req, $opt, $api['method']);
        if (!$v) {
            $this->respond->invalidRequest();
        }
    }

}