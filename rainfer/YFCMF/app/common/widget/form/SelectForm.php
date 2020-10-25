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
 * 单选下拉表单
 * @Author: rainfer <rainfer520@qq.com>
 */
class SelectForm
{
    protected $default = [
        'name'        => '',//name
        'title'       => '',//标签
        'options'     => [],
        'value'       => '',//值
        'attr'        => [
            'title'        => '',//显示title
            'disabled'     => false,
            'label_class'  => 'col-sm-3',
            'div_class'    => 'col-sm-9',
            'is_formgroup' => true, //是否form-group表单
            'default'      => '请选择'
        ],//属性
        'help_text'   => '',//帮助文本
        'extra_class' => 'col-xs-10 col-sm-5',//input的class
        'extra_attr'  => '',//input的额外属性
    ];

    /**
     * 渲染
     *
     * @param string $name        下拉菜单名
     * @param string $title       标题
     * @param array  $options
     * @param string $default     默认值
     * @param string $help_text   帮助文本
     * @param string $extra_attr  额外属性
     * @param array  $attr        属性
     * @param string $extra_class 额外css类名
     *
     * @return string
     */
    public function fetch($name, $title, $options = [], $default = '', $help_text = '', $extra_attr = '', $attr = [], $extra_class = 'col-xs-10 col-sm-5')
    {
        $data         = [
            'name'        => $name,//na
            'title'       => $title,//标
            'options'     => $options,
            'value'       => $default,//值
            'attr'        => $attr,//input的附加属性
            'help_text'   => $help_text,//帮助文本
            'extra_class' => $extra_class,//input的class
            'extra_attr'  => $extra_attr//input的css
        ];
        $data['attr'] = $data['attr'] ? array_merge($this->default['attr'], $data['attr']) : $this->default['attr'];
        $data         = array_merge($this->default, $data);
        $html         = '';
        if ($data['attr']['is_formgroup']) {
            $html = '<div class="form-group"><label class="' . $data['attr']['label_class'] . ' control-label no-padding-right"> ' . $data['title'] . ' </label><div class="' . $data['attr']['div_class'] . '">';
        }
        $html .= '<select name="' . $data['name'] . '" id="select-' . $data['name'] . '" title="' . $data['attr']['title'] . '" ' . ($data['attr']['disabled'] ? 'disabled' : '') . ' class="' . $data['extra_class'] . '" ' . $data['extra_attr'] . ' >';
        if ($data['attr']['default']) {
            $html .= '<option value="" ' . (($data['value'] === "") ? 'selected="selected"' : '') . '>' . $data['attr']['default'] . '</option>';
        }
        if ($data['options']) {
            foreach ($data['options'] as $key => $vo) {
                if (is_assoc($data['options']) || is_numeric($key)) {
                    $html .= '<option value="' . $key . '" ' . (($data['value'] == $key && $data['value'] !== '') ? 'selected="selected"' : '') . '>' . $vo . '</option>';
                } else {
                    $html .= '<option value="' . $vo . '" ' . (($data['value'] == $vo) ? 'selected="selected"' : '') . '>' . $vo . '</option>';
                }
            }
        }
        $html .= '</select>';
        if ($data['attr']['is_formgroup']) {
            if ($data['help_text']) {
                $html .= '<span class="middle help-text">' . $data['help_text'] . '</span>';
            }
            $html .= '</div></div>';
        }
        return $html;
    }
}
