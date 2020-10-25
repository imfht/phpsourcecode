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
 * 颜色选择表单
 * @Author: rainfer <rainfer520@qq.com>
 */
class ColorForm
{
    protected $default = [
        'name'        => '',//name
        'title'       => '',//标签
        'format'      => 'hex',
        'value'       => '',//值
        'attr'        => [
            'placeholder' => '',//预设值
            'disabled'    => false,//是否只读
            'tips'        => '',//提示
            'label_class' => 'col-sm-3',//标签class
            'div_class'   => 'col-sm-9'//input上层div的class
        ],//属性
        'help_text'   => '',//帮助文本
        'extra_class' => 'col-xs-10 col-sm-5',//input的class
        'extra_attr'  => ''//input的css
    ];

    /**
     * 渲染
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param string $default     默认值
     * @param string $help_text   帮助文本
     * @param string $format      格式
     * @param array  $attr        属性
     * @param string $extra_class 额外css类
     * @param string $extra_attr  额外属性
     *
     * @return string
     */
    public function fetch($name, $title, $default = '', $help_text = '', $format = 'hex', $attr = [], $extra_class = 'col-xs-10 col-sm-5', $extra_attr = '')
    {
        $data         = [
            'name'        => $name,//name
            'title'       => $title,//标签
            'value'       => $default,//值
            'format'      => $format,
            'attr'        => $attr,//属性
            'help_text'   => $help_text,//帮助文本
            'extra_class' => $extra_class,//input的class
            'extra_attr'  => $extra_attr//input的css
        ];
        $data['attr'] = isset($data['attr']) ? array_merge($this->default['attr'], $data['attr']) : $this->default['attr'];
        $data         = array_merge($this->default, $data);
        $html         = '<div class="form-group">';
        $html .= '<label class="' . $data['attr']['label_class'] . ' control-label no-padding-right"> ' . $data['title'] . ' </label>';
        $html .= '<div class="' . $data['attr']['div_class'] . '">';
        $html .= '<input type="text" name="' . $data['name'] . '" value="' . $data['value'] . '" id="color-' . $data['name'] . '" class="color-picker ' . $data['extra_class'] . '" style=" ' . $data['extra_attr'] . '" placeholder="' . $data['attr']['placeholder'] . '" ' . ($data['attr']['disabled'] ? 'disabled="disabled" ' : '') . ' ' . ($data['attr']['tips'] ? 'data-rel="tooltip" data-placement="bottom" data-original-title="' . $data['attr']['tips'] . '" ' : '') . ' data-date-format="' . $data['format'] . '" />';
        $html .= '<span class="btn-colorpicker"></span>';
        if ($data['help_text']) {
            $html .= '<span class="middle help-text">' . $data['help_text'] . '</span>';
        }
        $html .= '</div></div>';
        return $html;
    }
}
