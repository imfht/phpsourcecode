<?php

/**
 * 表单列表HTML
 */
namespace app\site\model;

class SiteFormHtmlListModel {

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
        return date('Y-m-d', $data);
    }

    public function time($data) {
        $time = explode(':', $data);
        return $time[0] . '点' . $time[1] . '分';
    }

    public function datetime($data) {
        return date('Y-m-d H:i:s', $data);
    }

    public function select($data, $config) {
        $data = target('site/SiteFormDataShow')->listSlt($data, $config);
        if(empty($data)) {
            return '空';
        }
        return $data[0]['name'];

    }

    public function radio($data, $config) {
        $data = target('site/SiteFormDataShow')->listSlt($data, $config);
        if(empty($data)) {
            return '空';
        }
        return $data[0]['name'];
    }

    public function checkbox($data, $config) {
        $list = target('site/SiteFormDataShow')->listSlt($data, $config);
        if(empty($list)) {
            return '空';
        }
        $html = [];
        foreach($list as $vo) {
            $html[] = $vo['name'];
        }
        return implode(',', $html);
    }

    public function image($data) {
        return "<img src='{$data}' width='120' height='80'>";
    }

    public function images($data) {
        $images = unserialize($data);
        return "<img src='{$images[0]['url']}' width='120' height='80'>";
    }

    public function file($data) {
        return $data;
    }

    public function files($data) {
        $files = unserialize($data);
        $html = '';
        foreach ($files as $key => $vo) {
            if ($key) {
                $html .= '<br>';
            }
            $html .= $vo['title']. '.' . $vo['ext'];
        }
        return $html;
    }

    public function price($data) {
        return price_format($data);
    }

    public function color($data) {
        return $data;
    }

    public function area($data) {
        $area = explode(',', $data);
        return $area[0] . ' - ' . $area[1] . ' - ' . $area[2];
    }

    public function baidumap($data) {
        return $data;
    }
}