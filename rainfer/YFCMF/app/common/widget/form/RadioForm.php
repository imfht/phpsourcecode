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
 * 单选表单
 * @Author: rainfer <rainfer520@qq.com>
 */
class RadioForm
{
    protected $default = [
        'name'        => '',//name
        'title'       => '',//标签
        'options'     => [],////['value'=>'name',...]
        'value'       => '',
        'disabled'    => [],
        'attr'        => ['size' => 'sm', 'label_class' => 'col-sm-3', 'div_class' => 'col-sm-9'],//属性
        'extra_attr'  => '',//input的额外属性
        'extra_class' => ''//input的额外class
    ];

    /**
     * 渲染
     *
     * @param string $name        单选名
     * @param string $title       单选标题
     * @param array  $options     单选数据
     * @param string $default     默认值
     * @param array  $disabled    单选禁止
     * @param array  $attr        属性，
     *                            size-尺寸(sm,lg)，默认sm
     *                            label_class(col-sm-x) 标签栅格样式,默认col-sm-3
     *                            div_class(col-sm-x) input上层栅格样式,默认col-sm-9
     * @param string $extra_class 额外css类名
     * @param string $extra_attr  额外属性
     *
     * @return string
     */
    public function fetch($name, $title, $options = [], $default = '', $disabled = [], $attr = [], $extra_class = '', $extra_attr = '')
    {
        $data         = [
            'name'        => $name,//name
            'title'       => $title,//标签
            'options'     => $options,//['value'=>'name',...]
            'value'       => $default,
            'disabled'    => $disabled,//[value,...]
            'attr'        => $attr,
            'extra_class' => $extra_class,//input的class
            'extra_attr'  => $extra_attr//input的css
        ];
        $data['attr'] = isset($data['attr']) ? array_merge($this->default['attr'], $data['attr']) : $this->default['attr'];
        $data         = array_merge($this->default, $data);
        $html         = '<div class="form-group">';
        $html .= '<label class="' . $data['attr']['label_class'] . ' control-label no-padding-right"> ' . $data['title'] . ' </label>';
        $html .= '<div class="' . $data['attr']['div_class'] . '">';
        if ($data['options']) {
            $i = 0;
            foreach ($data['options'] as $key => $vo) {
                $i++;
                $html .= '<div class="radio radio-inline"><label class="no-padding-left">';
                $html .= '<input name="' . $data['name'] . '" id="radio-' . $data['name'] . '-' . $i . '" value="' . $key . '" type="radio" class="ace input-' . $data['attr']['size'] . ' ' . $data['extra_class'] . '" ' . (($data['value'] == $key) ? 'checked' : '') . ' ' . (($data['disabled'] && in_array($key, $data['disabled'])) ? 'disabled' : '') . ' style="' . $data['extra_attr'] . '" >';
                $html .= '<span class="lbl"> ' . $vo . '</span></label></div>';
            }
        }
        $html .= '</div></div>';
        return $html;
    }
}
