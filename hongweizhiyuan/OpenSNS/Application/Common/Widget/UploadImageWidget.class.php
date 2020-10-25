<?php
/**
 * 所属项目 商业版.
 * 开发者: 陈一枭
 * 创建日期: 14-9-11
 * 创建时间: 下午1:09
 * 版权所有 想天软件工作室(www.ourstu.com)
 */

namespace Common\Widget;


use Think\Controller;

class UploadImageWidget extends Controller
{

    public function render($attributes = array())
    {

        $attributes_id = $attributes['id'];
        $config = $attributes['config'];
        $class = $attributes['class'];
        $value = $attributes['value'];
        $name = $attributes['name'];
        $width = $attributes['width'] ? $attributes['width'] : 100;
        $height = $attributes['height'] ? $attributes['height'] : 100;

        //$filetype = $this->rules['filetype'];

        $config = $config['config'];

        $id = $attributes_id;
        $attributes['config'] = array('text' => '选择文件'
        );


        if (intval($value) != 0) {
            $url = getThumbImageById($value, $width, $height);
            $img = '<img src="' . $url . '"/>';
        } else {
            $img = '';
        }

        $this->assign('img',$img);
        $this->assign($attributes);
        $this->display(T('Application://Common@Widget/uploadimage'));
    }
} 