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
 * 单文本表单
 * @Author: rainfer <rainfer520@qq.com>
 */
class TextForm
{
    protected $default = [
        'name'        => '',//name
        'title'       => '',//标签
        'type'        => 'text',//类型,默认文本
        'value'       => '',//值
        'attr'        => [
            'placeholder'  => '',//预设值
            'disabled'     => false,//是否只读
            'tips'         => '',//提示
            'icon'         => ['left' => '', 'right' => ''],//前置或后置图标只能1个,icon与addon_icon只能1个
            'addon_icon'   => ['left' => '', 'right' => ''],//前置或后置按钮组，允许2个
            'label_class'  => 'col-sm-3',//标签class
            'div_class'    => 'col-sm-9',//input上层div的class
            'is_formgroup' => true, //是否form-group表单
            'id'           => ''
        ],//属性
        'help_text'   => '',//帮助文本
        'extra_class' => 'col-xs-10 col-sm-5',//input的class
        'extra_css'   => '',//input的额外style
        'extra_attr'  => ''//input的额外属性
    ];

    /**
     * 渲染
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param string $default     默认值
     * @param string $help_text   帮助文
     * @param string $extra_attr  额外属性
     * @param string $type        默认值
     * @param array  $attr        属性本
     * @param string $extra_class 额外css类
     * @param string $extra_css   额外style
     *
     * @return string
     */
    public function fetch($name, $title, $default = '', $help_text = '', $extra_attr = '', $type = 'text', $attr = [], $extra_class = 'col-xs-10 col-sm-5', $extra_css = '')
    {
        $data         = [
            'name'        => $name,
            'title'       => $title,
            'type'        => $type,
            'value'       => $default,
            'attr'        => $attr,
            'help_text'   => $help_text,
            'extra_class' => $extra_class,
            'extra_css'   => $extra_css,
            'extra_attr'  => $extra_attr
        ];
        $data['attr'] = isset($data['attr']) ? array_merge($this->default['attr'], $data['attr']) : $this->default['attr'];
        $data         = array_merge($this->default, $data);
        $html         = '';
        if ($data['attr']['is_formgroup']) {
            $has_icon = $has_addicon = false;
            if ($data['type'] != 'hidden') {
                $html .= '<div class="form-group">';
                $html .= '<label class="' . $data['attr']['label_class'] . ' control-label no-padding-right"> ' . $data['title'] . ' </label>';
                $html .= '<div class="' . $data['attr']['div_class'] . '">';
                //判断是否有icon
                if ($data['attr']['icon'] && ($data['attr']['icon']['left'] || $data['attr']['icon']['right'])) {
                    $has_icon = true;
                    $html .= '<div class="input-group ' . $data['extra_class'] . '">';
                    if ($data['attr']['icon']['left']) {
                        $html .= '<span class="input-icon">';
                    } else {
                        $html .= '<span class="input-icon input-icon-right">';
                    }
                } elseif ($data['attr']['addon_icon'] && ($data['attr']['addon_icon']['left'] || $data['attr']['addon_icon']['right'])) {
                    $has_addicon = true;
                    $html .= '<div class="input-group ' . $data['extra_class'] . '">';
                    if ($data['attr']['addon_icon']['left']) {
                        $html .= '<span class="input-group-addon"><i class="ace-icon ' . $data['attr']['addon_icon']['left'] . '"></i></span>';
                    }
                }
            }
            $html .= '<input type="' . $data['type'] . '" name="' . $data['name'] . '" value="' . $data['value'] . '" id="' . ($data['attr']['id'] ?: "text-" . $data['name']) . '" class="' . ((!$has_icon && !$has_addicon) ? $data['extra_class'] : 'col-xs-12') . '" style=" ' . $data['extra_css'] . '" placeholder="' . $data['attr']['placeholder'] . '" ' . ($data['attr']['disabled'] ? 'disabled="disabled" ' : '') . ' ' . ($data['attr']['tips'] ? 'data-rel="tooltip" data-placement="bottom" data-original-title="' . $data['attr']['tips'] . '" ' : '') . ' ' . $data['extra_attr'] . ' >';
            if ($data['type'] != 'hidden') {
                if ($has_icon) {
                    if ($data['attr']['icon']['left']) {
                        $html .= '<i class="ace-icon ' . $data['attr']['icon']['left'] . '"></i></span>';
                    } else {
                        $html .= '<i class="ace-icon ' . $data['attr']['icon']['right'] . '"></i></span>';
                    }
                } elseif ($has_addicon && $data['attr']['addon_icon']['right']) {
                    $html .= '<span class="input-group-addon"><i class="ace-icon ' . $data['attr']['addon_icon']['right'] . '"></i></span>';
                }
                if ($has_icon || $has_addicon) {
                    $html .= '</div>';
                }
                if ($data['help_text']) {
                    $html .= '<span class="middle ' . ((!$has_icon && !$has_addicon) ? 'help-text' : '') . '">' . $data['help_text'] . '</span>';
                }
                $html .= '</div></div>';
            }
        } else {
            $html .= '<input type="' . $data['type'] . '" name="' . $data['name'] . '" value="' . $data['value'] . '" id="text-' . $data['name'] . '" class="' . $data['extra_class'] . '" style=" ' . $data['extra_css'] . '" placeholder="' . $data['attr']['placeholder'] . '" ' . ($data['attr']['disabled'] ? 'disabled="disabled" ' : '') . ' ' . ($data['attr']['tips'] ? 'data-rel="tooltip" data-placement="bottom" data-original-title="' . $data['attr']['tips'] . '" ' : '') . ' ' . $data['extra_attr'] . ' >';
        }
        return $html;
    }
}
