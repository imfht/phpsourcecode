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
 * 多行文本框
 * @Author: rainfer <rainfer520@qq.com>
 */
class TextAreaForm
{
    protected $default = [
        'name'        => '',//name
        'title'       => '',//标签
        'value'       => '',//值
        'attr'        => [
            'placeholder' => '',
            'rows'        => 3,
            'maxlength'   => 0,
            'autosize'    => false,
            'disabled'    => false,
            'tips'        => '',
            'label_class' => 'col-sm-3',
            'div_class'   => 'col-sm-9'
        ],
        'help_text'   => '',//帮助文本
        'extra_class' => 'col-xs-10 col-sm-5',//input的class
        'extra_attr'  => '',//input的css
    ];

    /**
     * 渲染
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param string $default     默认值
     * @param string $help_text   帮助文本
     * @param string $extra_attr  额外属性
     * @param array  $attr        属性
     * @param string $extra_class 额外css类名
     *
     * @return string
     */
    public function fetch($name, $title, $default = '', $help_text = '', $extra_attr = '', $attr = [], $extra_class = 'col-xs-10 col-sm-5')
    {
        $data         = [
            'name'        => $name,
            'title'       => $title,
            'value'       => $default,
            'attr'        => $attr,
            'help_text'   => $help_text,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr
        ];
        $data['attr'] = isset($data['attr']) ? array_merge($this->default['attr'], $data['attr']) : $this->default['attr'];
        $data         = array_merge($this->default, $data);
        $html         = '<div class="form-group">';
        $html .= '<label class="' . $data['attr']['label_class'] . ' control-label no-padding-right"> ' . $data['title'] . ' </label>';
        $html .= '<div class="' . $data['attr']['div_class'] . '">';
        $html .= '<textarea type="text" name="' . $data['name'] . '" ' . 'id="textarea-' . $data['name'] . '" class="' . $data['extra_class'] . ' ' . ($data['attr']['maxlength'] ? 'limited' : '') . ' ' . ($data['attr']['autosize'] ? 'autosize' : '') . '" style=" ' . $data['extra_attr'] . '" placeholder="' . $data['attr']['placeholder'] . '" ' . ($data['attr']['disabled'] ? 'disabled="disabled" ' : '') . ' ' . ($data['attr']['tips'] ? 'data-rel="tooltip" data-placement="bottom" data-original-title="' . $data['attr']['tips'] . '" ' : '') . ' ' . ($data['attr']['maxlength'] ? 'maxlength="' . $data['attr']['maxlength'] . '"' : '') . ' ' . ($data['attr']['autosize'] ? '' : 'rows="' . $data['attr']['rows'] . '"') . '  />' . $data['value'] . '</textarea>';
        if ($data['help_text']) {
            $html .= '<span class="middle help-text">' . $data['help_text'] . '</span>';
        }
        $html .= '</div></div>';
        return $html;
    }
}
