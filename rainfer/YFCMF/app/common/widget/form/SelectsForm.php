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
 * 多选下拉表单
 * @Author: rainfer <rainfer520@qq.com>
 */
class SelectsForm
{
    protected $default = [
        'name'        => '',//name
        'title'       => '',//标签
        'optgroups'   => [],
        /*  optgroups格式
         [
            [
                'label'=>'',
                'options'=>[
                    [
                        'divider'=>false,
                        'title'=>'',
                        'value'=>'',
                        'name'=>'',
                        'class'=>'',
                        'style'=>'',
                        'icon'=>'',
                        'subtext'=>'',
                        'disabled'=>false
                    ] ,
                    []
                 ],
                'max'=>0,
                'disabled'=>false
            ],
            []
        ];
         */
        'options'     => [],
        'value'       => '',//值,多个以,隔开
        'attr'        => [
            'placeholder' => '请选择',
            'multiple'    => true,
            'size'        => 5,
            'format'      => '',//'values' ,'count', 'count > x', 'static'
            'max'         => 0,
            'btn_class'   => 'info',//primary, info, success, warning, danger,
            'disabled'    => false,
            'label_class' => 'col-sm-3',
            'div_class'   => 'col-sm-9'
        ],//属性
        'help_text'   => '',//帮助文本
        'extra_class' => '',//input的class
        'extra_attr'  => '',//input的css
    ];

    /**
     * 渲染
     *
     * @param string $name        下拉菜单名
     * @param string $title       标题
     * @param array  $options     选项(普通情况使用) ['value'=>'name','divider',...],'divider'表示为分隔线
     *                            $optgroups、$options选择1个，普通选择$options，复杂含分组选$optgroups
     * @param string $default     默认值 多个值以,隔开
     * @param string $help_text   帮助文本
     * @param string $extra_attr  额外属性
     * @param array  $optgroups   选项(复杂含分组时使用)
     *                            optgroups格式
     *                            [
     *                            [
     *                            'label'=>'',
     *                            'options'=>[
     *                            [
     *                            'divider'=>false,
     *                            'title'=>'',
     *                            'value'=>'',
     *                            'name'=>'',
     *                            'class'=>'',
     *                            'style'=>'',
     *                            'icon'=>'',
     *                            'subtext'=>'',
     *                            'disabled'=>false
     *                            ] ,
     *                            []
     *                            ],
     *                            'max'=>0,
     *                            'disabled'=>false
     *                            ],
     *                            []
     *                            ];
     * @param array  $attr        属性
     * @param string $extra_class 额外css类名
     *
     * @return string
     */
    public function fetch($name, $title, $options = [], $default = '', $help_text = '', $extra_attr = '', $optgroups = [], $attr = [], $extra_class = '')
    {
        $data         = [
            'name'        => $name,//na
            'title'       => $title,//标
            'optgroups'   => $optgroups,
            'options'     => $options,
            'value'       => $default,//值
            'attr'        => $attr,//input的附加属性
            'help_text'   => $help_text,//帮助文本
            'extra_class' => $extra_class,//input的class
            'extra_attr'  => $extra_attr//input的css
        ];
        $data['attr'] = $data['attr'] ? array_merge($this->default['attr'], $data['attr']) : $this->default['attr'];
        $data         = array_merge($this->default, $data);
        $html         = '<div class="form-group"><label class="' . $data['attr']['label_class'] . ' control-label no-padding-right"> ' . $data['title'] . ' </label><div class="' . $data['attr']['div_class'] . '">';
        $html .= '<select name="' . $data['name'] . '" ' . ($data['attr']['max'] ? 'data-max-options="' . $data['attr']['max'] . '"' : '') . ' id="select-' . $data['name'] . '" title="' . $data['attr']['placeholder'] . '" ' . ($data['attr']['disabled'] ? 'disabled' : '') . ' data-style="btn-' . $data['attr']['btn_class'] . '" data-value="' . $data['value'] . '" class="input-select ' . $data['extra_class'] . '" ' . ($data['attr']['multiple'] ? 'multiple' : '') . ' data-width="fit" data-size="' . $data['attr']['size'] . '" ' . ($data['attr']['format'] ? 'data-selected-text-format="' . $data['attr']['format'] . '"' : '') . ' style=" ' . $data['extra_attr'] . '" >';
        if ($data['optgroups']) {
            foreach ($data['optgroups'] as $vo) {
                if (isset($vo['label']) && $vo['label']) {
                    $html .= '<optgroup label="' . $vo['label'] . '" ' . ((isset($vo['max']) && $vo['max']) ? 'data-max-options="' . $vo['max'] . '"' : '') . ' ' . ((isset($vo['disabled']) && $vo['disabled']) ? 'disabled' : '') . ' >';
                }
                if (isset($vo['options']) && $vo['options']) {
                    foreach ($vo['options'] as $vvo) {
                        if (isset($vvo['divider']) && $vvo['divider']) {
                            $html .= '<option data-divider="true"></option>';
                        } else {
                            $html .= '<option ' . ((isset($vvo['class']) && $vvo['class']) ? 'class="' . $vvo['class'] . '"' : '') . ' ' . ((isset($vvo['style']) && $vvo['style']) ? 'style="' . $vvo['style'] . '"' : '') . ' ' . ((isset($vvo['icon']) && $vvo['icon']) ? 'data-icon="' . $vvo['icon'] . '"' : '') . ' ' . ((isset($vvo['subtext']) && $vvo['subtext']) ? 'data-subtext="' . $vvo['subtext'] . '"' : '') . ' ' . ((isset($vvo['disabled']) && $vvo['disabled']) ? 'disabled' : '') . ' ' . ((isset($vvo['title']) && $vvo['title']) ? 'title="' . $vvo['title'] . '"' : '') . ' ' . (isset($vvo['value']) ? 'value="' . $vvo['value'] . '"' : '') . ' >' . $vvo['name'] . '</option>';
                        }
                    }
                }
                if (isset($vo['label']) && $vo['label']) {
                    $html .= '</optgroup>';
                }
            }
        } elseif ($data['options']) {
            foreach ($data['options'] as $vo) {
                if ($vo == 'divider') {
                    $html .= '<option data-divider="true"></option>';
                } else {
                    if (is_array($vo) && is_assoc($vo)) {
                        $html .= '<option ' . ((isset($vo['class']) && $vo['class']) ? 'class="' . $vo['class'] . '"' : '') . ' ' . ((isset($vo['style']) && $vo['style']) ? 'style="' . $vo['style'] . '"' : '') . ' ' . ((isset($vo['icon']) && $vo['icon']) ? 'data-icon="' . $vo['icon'] . '"' : '') . ' ' . ((isset($vo['subtext']) && $vo['subtext']) ? 'data-subtext="' . $vo['subtext'] . '"' : '') . ' ' . ((isset($vo['disabled']) && $vo['disabled']) ? 'disabled' : '') . ' ' . ((isset($vo['title']) && $vo['title']) ? 'title="' . $vo['title'] . '"' : '') . ' ' . (isset($vo['value']) ? 'value="' . $vo['value'] . '"' : '') . ' >' . $vo['name'] . '</option>';
                    } else {
                        $html .= '<option value="' . $vo . '">' . $vo . '</option>';
                    }
                }
            }
        }
        $html .= '</select>';
        if ($data['help_text']) {
            $html .= '<span class="middle help-text">' . $data['help_text'] . '</span>';
        }
        $html .= '</div></div>';
        return $html;
    }
}
