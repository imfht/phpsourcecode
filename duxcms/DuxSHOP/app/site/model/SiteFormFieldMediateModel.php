<?php

/**
 * 表单字段预处理
 */
namespace app\site\model;

class SiteFormFieldMediateModel {

    protected $updateData = [];


    public function image($field, $config) {
        $this->upload();
        if(empty($this->updateData)) {
            return [];
        }
        $info = $this->updateData[$field];
        return $info['url'];
    }

    public function images($field, $config) {
        $this->upload();
        $list = [];
        if(empty($this->updateData)) {
            return [];
        }
        foreach($this->updateData as $key => $vo) {
            if(strpos($key, $field . '-', 0) !== false) {
                $list[] = $vo;
            }
        }
        if(empty($list)) {
            return [];
        }
        $data = [];
        foreach($list as $v) {
            $data['url'][] = $v['url'];
            $data['title'][] = $v['title'];
        }

        return $data;
    }

    public function file($field, $config) {
        $this->upload();
        if(empty($this->updateData)) {
            return [];
        }
        return $this->updateData[$field]['url'];
    }

    public function files($field, $config) {
        $this->upload();
        if(empty($this->updateData)) {
            return [];
        }
        $list = [];
        foreach($this->updateData as $key => $vo) {
            if(strpos($key, $field, 0)) {
                $list[] = $vo;
            }
        }
        if(empty($list)) {
            return [];
        }
        $data = [];
        foreach($list as $v) {
            $data[$field]['url'][] = $v['url'];
            $data[$field]['title'][] = $v['title'];
            $data[$field]['ext'][] = $v['ext'];
            $data[$field]['size'][] = $v['size'];
        }
        return $data;
    }

    private function upload() {
        if(empty($this->updateData)) {
            $this->updateData = target('base/Upload')->upload();
        }
    }
}