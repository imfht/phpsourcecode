<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Passport {
    /// 用户登录
    public function login(Request $request, Respond $respond) {
        $CI = &get_instance();
        $CI->load->model("user_model");
        $CI->load->helper('regexp');

        $type = null;
        $password = $request->params('password');
        $passport = $request->params('passport');
        if ($passport == null) {
            $passport = $request->params('phone');
        }
        if ($passport == null) {
            $passport = $request->params('email');
        }
        if ($passport == null) {
            $passport = $request->params('account');
        }

        if (regexp('account', $passport)) {
            $uid = $CI->user_model->verify_account($passport, $password);
        } elseif (regexp('email', $passport)) {
            $uid = $CI->user_model->verify_email($passport, $password);
        } elseif (regexp('cn_phone', $passport)) {
            $uid = $CI->user_model->verify_phone($passport, $password);
        } else {
            $uid = null;
        }

        if ($uid) {
            $respond->setCode(20000);
            $CI->load->library('token/jwt');

            $header = json_decode($request->params('header'), true);
            $header = $header ? $header : $CI->jwt->initHeader();

            $payload = array('id' => $uid);

            $token = $CI->jwt->generator($header, $payload);
            $respond->setData(array('token' => $token));
        }
        return $respond;
    }

    /// 登录信息
    public function info(Request $request, Respond $respond) {
        $CI = &get_instance();
        $CI->load->library('token/jwt');
        $CI->load->model("user_model");
        $CI->load->model("role_model");
        $token = $request->params('token');
        $token = $CI->jwt->validator($token);
        $payload = $token['payload'];
        if ($token && $payload['exp'] > time()) {
            $info = $CI->user_model->getInfo($payload['id']);
            if ($info) {
                $info['roles'] = $CI->role_model->getRoles($payload['id']);
                $respond->setCode(20000)->setData($info);
            }
        } else {
            $respond->setCode(40099);
        }
        return $respond;
    }

    public function refresh(Request $request, Respond $respond) {
        $CI = &get_instance();

        $CI->load->library('token/jwt');

        $old_token = $request->get_token();

        $tkr = explode('.', $old_token);
        $header = json_decode(url64_decode($tkr[0]), true);

        $payload = $request->payload();
        $token = $CI->jwt->generator($header, $payload);
        $respond->setData(array('header' => $header, 'payload' => $payload, 'token' => $token));
        if ($token) {
            $respond->setCode(20000);
            $respond->setData(array('token' => $token));
        }
        return $respond;
    }

    // 注销登录
    public function logout(Request $request, Respond $respond) {
        $respond->setCode(20000);
        return $respond;
    }


}