<?php

/**
 * 表单字段验证
 */
namespace app\site\model;

class SiteFormFieldValidateModel {

    public function text($data, $config) {
        if(empty($data)) {
            return false;
        }
        $config = explode(',', $config, 2);
        $min = 0;
        $max = 0;
        if ($config[0]) {
            $min = intval($config[0]);
        }
        if ($config[1]) {
            $max = intval($config[1]);
        }

        if (function_exists('mb_strlen')) {
            $len = mb_strlen($data, 'utf8');
        } else {
            $len = strlen($data);
        }
        if($min) {
            if($len < $min) {
                return false;
            }
        }
        if($max) {
            if($len > $max) {
                return false;
            }
        }
        return true;
    }

    public function number($data, $config) {
        if(empty($data)) {
            return false;
        }
        $config = explode(',', $config, 2);
        $min = 0;
        $max = 0;
        if ($config[0]) {
            $min = intval($config[0]);
        }
        if ($config[1]) {
            $max = intval($config[1]);
        }
        if($min && $data < $min) {
            return false;
        }
        if($max && $data > $max) {
            return false;
        }
        return true;
    }

    public function phone($data, $config) {
        if(empty($data)) {
            return false;
        }
        if (empty($config)) {
            $reg = '/(^1[3|4|5|7|8][0-9]{9}$)/';
        } else {
            $config = explode(',', $config);
            $configReg = implode('|', $config);
            $reg = '/(^[' . $configReg . '][0-9]{8}$)/';
        }
        if (preg_match($reg, $data)) {
            return true;
        }else {
            return false;
        }
    }

    public function tel($data, $config) {
        if(empty($data)) {
            return false;
        }
        if (empty($config)) {
            $reg = '/^([0-9]{3,4}-)?[0-9]{7,8}$/';
        } else {
            $config = explode(',', $config);
            $configReg = implode('|', $config);
            $reg = '/^([' . $configReg . ']-)?[0-9]{7,8}$/';
        }
        if (preg_match($reg, $data)) {
            return true;
        }else {
            return false;
        }
    }

    public function email($data, $config) {
        if(empty($data)) {
            return false;
        }
        if (empty($config)) {
            $reg = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
        } else {
            $config = explode(',', $config);
            $configReg = implode('|', $config);
            $reg = '/\w+([-+.]\w+)*@[' . $configReg . ']\.\w+([-.]\w+)*/';
        }
        if (preg_match($reg, $data)) {
            return true;
        }else {
            return false;
        }
    }

    public function textarea($data, $config) {
        return $this->text($data, $config);
    }

    public function editor($data, $config) {
        return $this->verifyEmpty($data);
    }

    public function date($data, $config) {
        return $this->verifyEmpty($data);
    }

    public function time($data, $config) {
        return $this->verifyEmpty($data);
    }

    public function datetime($data, $config) {
        return $this->verifyEmpty($data);
    }

    public function select($data, $config) {
        if(empty($data) && $data <> '0') {
            return false;
        }else{
            return true;
        }
    }

    public function radio($data, $config) {
        if(empty($data) && $data <> '0') {
            return false;
        }else{
            return true;
        }
    }

    public function checkbox($data, $config) {
        if(empty($data) && $data <> '0') {
            return false;
        }else{
            return true;
        }
    }

    public function image($data, $config) {
        if(empty($data)) {
            return false;
        }
        $urlParam = explode('.', $data);
        $ext = end($urlParam);
        $exts = ['jpg', 'bmp', 'jpeg', 'png', 'gif'];
        if(!in_array(strtolower($ext), $exts)) {
            return false;
        }
        return true;
    }

    public function images($data, $config) {
        $data = unserialize($data);
        $data = array_filter((array)$data);
        if(empty($data)) {
            return false;
        }
        $exts = ['jpg', 'bmp', 'jpeg', 'png', 'gif'];
        foreach($data as $vo) {
            $urlParam = explode('.', $vo['url']);
            $ext = end($urlParam);
            if(!in_array(strtolower($ext), $exts)) {
                return false;
            }
        }
        return true;
    }

    public function file($data, $config) {
        if(empty($data)) {
            return false;
        }
        $urlParam = explode('.', $data);
        $ext = end($urlParam);
        if($config) {
            $exts = implode(',', $config);
            if(!in_array(strtolower($ext), $exts)) {
                return false;
            }
        }
        return true;
    }

    public function files($data, $config) {
        $data = unserialize($data);
        $data = array_filter((array)$data);
        if(empty($data)) {
            return false;
        }
        if($config) {
            $exts = implode(',', $config);
            foreach($data as $vo) {
                $urlParam = explode('.', $vo['url']);
                $ext = end($urlParam);
                if(!in_array(strtolower($ext), $exts)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function price($data, $config) {
        return $this->number($data, $config);
    }

    public function color($data, $config) {
        return $this->verifyEmpty($data);
    }

    public function area($data, $config) {
        return $this->verifyEmpty($data);
    }

    public function baidumap($data, $config) {
        return $this->verifyEmpty($data);
    }

    protected function verifyEmpty($data) {
        if (empty($data)) {
            return false;
        }
        return true;
    }
}