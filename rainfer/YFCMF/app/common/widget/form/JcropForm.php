<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------

namespace app\common\widget\form;

/**
 * 图片剪裁表单
 * @Author: rainfer <rainfer520@qq.com>
 */
class JcropForm
{
    protected $default = [
        'name'        => '',
        'title'       => '',
        'value'       => '',
        'attr'        => [
            'id'          => '',
            'label_class' => 'col-sm-3',//标签class
            'div_class'   => 'col-sm-9'//input上层div的class
        ],
        'help_text'   => '',//帮助文本
        'extra_class' => 'col-xs-10 col-sm-5'
    ];

    /**
     * 渲染
     *
     * @param string $name
     * @param string $title 标题
     * @param string $default
     * @param array  $attr  属性，
     * @param string $extra_class
     *
     * @return string
     */
    public function fetch($name = '', $title = '', $default = '', $attr = [], $extra_class = 'col-xs-10 col-sm-5')
    {
        $data         = [
            'name'        => $name,//id
            'title'       => $title,//标签
            'value'       => $default,
            'attr'        => $attr,
            'extra_class' => $extra_class
        ];
        $data['attr'] = isset($data['attr']) ? array_merge($this->default['attr'], $data['attr']) : $this->default['attr'];
        $data         = array_merge($this->default, $data);
        $html         = '<div class="form-group">';
        $html .= '<label class="' . $data['attr']['label_class'] . ' control-label no-padding-right"> ' . $data['title'] . ' </label>';
        $html .= '<div class="' . $data['attr']['div_class'] . '">';
        $html .= '<div class="no-padding ' . $data['extra_class'] . '">';
        if (!$data['attr']['id']) {
            $data['attr']['id'] = 'jcrop_' . $data['name'];
        }
        $html .= '<div class="file-item thumbnail">';
        $html .= '<input name="' . $data['name'] . '" id="' . $data['attr']['id'] . '_input" type="hidden" value="' . $data['value'] . '" />';
        if (!$data['value']) {
            $data['value'] = __ROOT__ . '/public/images/no_img.jpg';
        }
        $html .= '<a class="img-link" href="javascript:void(0);">';
        $html .= '<img src="' . $data['value'] . '" id="' . $data['attr']['id'] . '_img" width="100" data-toggle="modal" data-target="#' . $data['attr']['id'] . '_modal" ></a>';
        $html .= '</div>';
        $html .= '</div></div></div>';
        return $html;
    }
}
