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
 * 范围表单
 * @Author: rainfer <rainfer520@qq.com>
 */
class RangeForm
{
    protected $default = [
        'name'        => '',//name
        'title'       => '',//标签
        'value'       => '',//值
        'attr'        => [
            'data_type'                   => 'single',
            'data_min'                    => '10',
            'data_max'                    => '100',
            'data_from'                   => '10',
            'data_to'                     => '100',
            'data_step'                   => '1',
            'data_min_interval'           => '',
            'data_max_interval'           => '',
            'data_drag_interval'          => false,
            'data_values'                 => '',
            'data_from_fixed'             => false,
            'data_from_min'               => '10',
            'data_from_max'               => '100',
            'data_from_shadow'            => false,
            'data_to_fixed'               => false,
            'data_to_min'                 => '10',
            'data_to_max'                 => '100',
            'data_to_shadow'              => false,
            'data_prettify_enabled'       => true,
            'data_prettify_separator'     => '',
            'data_force_edges'            => false,
            'data_keyboard'               => true,
            'data_grid'                   => true,
            'data_grid_margin'            => true,
            'data_grid_num'               => '4',
            'data_grid_snap'              => false,
            'data_hide_min_max'           => false,
            'data_hide_from_to'           => false,
            'data_prefix'                 => '',
            'data_postfix'                => '',
            'data_max_postfix'            => '',
            'data_decorate_both'          => true,
            'data_input_values_separator' => ';',
            'data_disable'                => false,
            'data_blokc'                  => false,
            'data_extra_classes'          => '',
            'label_class'                 => 'col-sm-3',
            'div_class'                   => 'col-sm-9',
            'tips'                        => ''
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
     * @param array  $attr        属性，//具体查看https://github.com/IonDen/ion.rangeSlider
     * @param string $extra_class 额外css类名
     * @param string $extra_attr  额外属性
     *
     * @return string
     */
    public function fetch($name, $title, $default = '0', $help_text = '', $attr = [], $extra_class = '', $extra_attr = '')
    {
        $data         = [
            'name'        => $name,//name
            'title'       => $title,//标签
            'value'       => $default,
            'attr'        => $attr,
            'help_text'   => $help_text,
            'extra_class' => $extra_class,//input的class
            'extra_attr'  => $extra_attr//input的css
        ];
        $data['attr'] = isset($data['attr']) ? array_merge($this->default['attr'], $data['attr']) : $this->default['attr'];
        $data         = array_merge($this->default, $data);
        $html         = '<div class="form-group">';
        $html .= '<label class="' . $data['attr']['label_class'] . ' control-label no-padding-right"> ' . $data['title'] . ' </label>';
        $html .= '<div class="' . $data['attr']['div_class'] . '">';
        $html .= '<span class="col-xs-10 col-sm-5 no-padding">';
        $option = 'data-type="' . $data['attr']['data_type'] . '"';
        $option .= 'data-min="' . $data['attr']['data_min'] . '"';
        $option .= 'data-max="' . $data['attr']['data_max'] . '"';
        $option .= 'data-from="' . $data['value'] . '"';//初始位置
        $option .= 'data-to="' . $data['attr']['data_to'] . '"';
        $option .= 'data-step="' . $data['attr']['data_step'] . '"';
        $option .= 'data-min-interval="' . $data['attr']['data_min_interval'] . '"';
        $option .= 'data-max-interval="' . $data['attr']['data_max_interval'] . '"';
        $option .= 'data-drag-interval="' . $data['attr']['data_drag_interval'] . '"';
        $option .= 'data-values="' . $data['attr']['data_values'] . '"';
        $option .= 'data-from-fixed="' . $data['attr']['data_from_fixed'] . '"';
        $option .= 'data-from-min="' . $data['attr']['data_from_min'] . '"';
        $option .= 'data-from-max="' . $data['attr']['data_from_max'] . '"';
        $option .= 'data-from-shadow="' . $data['attr']['data_from_shadow'] . '"';
        $option .= 'data-to-fixed="' . $data['attr']['data_to_fixed'] . '"';
        $option .= 'data-to-min="' . $data['attr']['data_to_min'] . '"';
        $option .= 'data-to-max="' . $data['attr']['data_to_max'] . '"';
        $option .= 'data-to-shadow="' . $data['attr']['data_to_shadow'] . '"';
        $option .= 'data-prettify-enabled="' . $data['attr']['data_prettify_enabled'] . '"';
        $option .= 'data-prettify-separator="' . $data['attr']['data_prettify_separator'] . '"';
        $option .= 'data-force-edges="' . $data['attr']['data_force_edges'] . '"';
        $option .= 'data-keyboard="' . $data['attr']['data_keyboard'] . '"';
        $option .= 'data-grid="' . $data['attr']['data_grid'] . '"';
        $option .= 'data-grid-margin="' . $data['attr']['data_grid_margin'] . '"';
        $option .= 'data-grid-num="' . $data['attr']['data_grid_num'] . '"';
        $option .= 'data-grid-snap="' . $data['attr']['data_grid_snap'] . '"';
        $option .= 'data-hide-min-max="' . $data['attr']['data_hide_min_max'] . '"';
        $option .= 'data-hide-from-to="' . $data['attr']['data_hide_from_to'] . '"';
        $option .= 'data-prefix="' . $data['attr']['data_prefix'] . '"';
        $option .= 'data-postfix="' . $data['attr']['data_postfix'] . '"';
        $option .= 'data-max-postfix="' . $data['attr']['data_max_postfix'] . '"';
        $option .= 'data-decorate-both="' . $data['attr']['data_decorate_both'] . '"';
        $option .= 'data-input-values-separator="' . $data['attr']['data_input_values_separator'] . '"';
        $option .= 'data-disable="' . $data['attr']['data_disable'] . '"';
        $option .= 'data-blokc="' . $data['attr']['data_blokc'] . '"';
        $option .= 'data-extra-classes="' . $data['attr']['data_extra_classes'] . '"';
        $html .= '<input type="text" name="' . $data['name'] . '" value="' . $data['value'] . '" id="range-' . $data['name'] . '" class="rangeslider ' . $data['extra_class'] . '" style=" ' . $data['extra_attr'] . '"  ' . ($data['attr']['tips'] ? 'data-rel="tooltip" data-placement="bottom" data-original-title="' . $data['attr']['tips'] . '" ' : '') . ' ' . $option . ' />';
        $html .= '</span>';
        if ($data['help_text']) {
            $html .= '<span class="middle help-text">' . $data['help_text'] . '</span>';
        }
        $html .= '</div></div>';
        return $html;
    }
}
