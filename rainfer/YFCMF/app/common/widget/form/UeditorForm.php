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

/*
 * 百度编辑框
 * @Author: rainfer <rainfer520@qq.com>
 */
class UeditorForm
{
    protected $default = [
        'name'        => '',
        'title'       => '',
        'value'       => '',
        'attr'        => [
            'label_class' => 'col-sm-3',//标签class
            'div_class'   => 'col-sm-9'//input上层div的class
        ],
        'help_text'   => '',//帮助文本
        'extra_class' => 'col-xs-12'
    ];

    /**
     * 渲染
     *
     * @param string $name
     * @param string $title 标题
     * @param string $default
     * @param string $help_text
     * @param array  $attr  属性，
     * @param string $extra_class
     *
     * @return string
     */
    public function fetch($name = '', $title = '', $default = '', $help_text = '', $attr = [], $extra_class = 'col-xs-10')
    {
        $data         = [
            'name'        => $name,//id
            'title'       => $title,//标签
            'value'       => $default,
            'attr'        => $attr,
            'help_text'   => $help_text,
            'extra_class' => $extra_class
        ];
        $data['attr'] = isset($data['attr']) ? array_merge($this->default['attr'], $data['attr']) : $this->default['attr'];
        $data         = array_merge($this->default, $data);
        $html         = '<div class="form-group">';
        $html .= '<label class="' . $data['attr']['label_class'] . ' control-label no-padding-right"> ' . $data['title'] . ' </label>';
        $html .= '<div class="' . $data['attr']['div_class'] . '">';
        $html .= '<div class="no-padding ' . $data['extra_class'] . '">';
        $html .= '<script id="' . $data['name'] . '" class="input-ueditor" name="' . $data['name'] . '" type="text/plain">' . $data['value'] . '</script></div>';
        if ($data['help_text']) {
            $html .= '<span style="color: #F3920A;">' . $data['help_text'] . '</span>';
        }
        $html .= '</div></div>';
        return $html;
    }
}
