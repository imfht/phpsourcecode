<?php

/**
 * 表单内容格式化
 */
namespace app\site\model;

class SiteFormDataShowModel {

    public function text($data) {
        return $data;
    }

    public function number($data) {
        return $data;
    }

    public function phone($data) {
        return $data;
    }

    public function tel($data) {
        return $data;
    }

    public function email($data) {
        return $data;
    }

    public function textarea($data) {
        return $data;
    }

    public function editor($data) {
        return $data;
    }

    public function date($data) {
        return $data;
    }

    public function time($data) {
        return explode(':', $data);
    }

    public function datetime($data) {
        return $data;
    }

    public function select($data, $config) {
        $data = $this->listSlt($data, $config);
        return $data[0];

    }

    public function radio($data, $config) {
        $data = $this->listSlt($data, $config);
        return $data[0];
    }

    public function checkbox($data, $config) {
        return $this->listSlt($data, $config);
    }

    public function image($data) {
        return $data;
    }

    public function images($data) {
        return unserialize($data);
    }

    public function file($data) {
        return $data;
    }

    public function files($data) {
        return unserialize($data);
    }

    public function price($data) {
        return price_format($data);
    }

    public function color($data) {
        return $data;
    }

    public function area($data) {
        return explode(',', $data);
    }

    public function baidumap($data) {
        return $data;
    }

    public function listSlt($data, $config = []) {
        $data = explode(',', $data);
        if(empty($data)) {
            return [];
        }
        $list = explode("\n", $config);
        $list = array_filter($list);
        if(empty($list)) {
            return [];
        }
        $value = [];
        foreach ($list as $vo) {
            $info = explode(',', $vo);
            if(in_array($info[0], $data)) {
                $value[] = [
                    'key' => $info[0],
                    'name' => $info[1],
                ];
            }
        }
        return $value;
    }
}