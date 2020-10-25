<?php

/**
 * 表单字段格式化
 */
namespace app\site\model;

class SiteFormFieldFormatModel {

    public function text($data, $config) {
        return html_in(html_clear($data));
    }

    public function number($data, $config) {
        return intval($data);
    }

    public function phone($data, $config) {
        return html_in(html_clear($data));
    }

    public function tel($data, $config) {
        return html_in(html_clear($data));
    }

    public function email($data, $config) {
        return html_in(html_clear($data));
    }

    public function textarea($data, $config) {
        return html_in(html_clear($data));
    }

    public function editor($data, $config) {
        return html_in($data);
    }

    public function date($data, $config) {
        if (empty($data)) {
            return '';
        }
        return strtotime($data);
    }

    public function time($data, $config) {
        if (empty($data)) {
            return '';
        }
        return html_in(html_clear($data));
    }

    public function datetime($data, $config) {
        if (empty($data)) {
            return '';
        }
        return strtotime($data);
    }

    public function select($data, $config) {
        return html_in(html_clear($data));
    }

    public function radio($data, $config) {
        return html_in(html_clear($data));
    }

    public function checkbox($data, $config) {
        return implode(',', $data);
    }

    public function image($data, $config) {
        return html_clear($data);
    }

    public function images($data, $config) {
        if (empty($data)) {
            return '';
        }
        $list = [];
        foreach ($data['url'] as $key => $vo) {
            $list[] = [
                'url' => $vo,
                'title' => $data['title'][$key]
            ];
        }
        return serialize($list);
    }

    public function file($data, $config) {
        return html_clear($data);
    }

    public function files($data, $config) {
        if (empty($data)) {
            return '';
        }
        $list = [];
        foreach ($data['url'] as $key => $vo) {
            $list[] = [
                'url' => $vo,
                'title' => $data['title'][$key],
                'ext' => $data['ext'][$key],
                'size' => $data['size'][$key]
            ];
        }
        return serialize($list);
    }

    public function price($data, $config) {
        return price_format($data);
    }

    public function color($data, $config) {
        return html_in(html_clear($data));
    }

    public function area($data, $config) {
        return implode(',', $data);
    }

    public function baidumap($data, $config) {
        return html_in(html_clear($data));
    }
}