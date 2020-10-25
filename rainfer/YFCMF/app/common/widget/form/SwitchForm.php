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
 * 开关表单
 * @Author: rainfer <rainfer520@qq.com>
 */
class SwitchForm
{
    protected $default = [
        'name'        => '',//name
        'title'       => '',//标签
        'value'       => 0,
        'attr'        => ['style' => '4', 'text' => ['ON', 'OFF'], 'btn' => 'flat', 'label_class' => 'col-sm-3', 'div_class' => 'col-sm-9', 'disabled' => false],//属性
        'extra_class' => '',//input的class
        'extra_attr'  => ''//input的css
    ];

    /**
     * 渲染
     *
     * @param string $name        表单项名
     * @param string $title       标题
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param array  $attr        属性，
     *                            style-形状(1,2,3,4,5,6,7)，默认4
     *                            text(['on','off']),默认为[]
     *                            btn('rotate','empty','flat'),默认'flat' 按钮样式
     *                            disabled 默认false
     * @param string $extra_class 额外css类名
     *
     * @return string
     */
    public function fetch($name, $title, $default = '0', $extra_attr = '', $attr = [], $extra_class = '')
    {
        $data         = [
            'name'        => $name,//name
            'title'       => $title,//标签
            'value'       => $default,
            'attr'        => $attr,
            'extra_class' => $extra_class,//input的class
            'extra_attr'  => $extra_attr//input的css
        ];
        $data['attr'] = isset($data['attr']) ? array_merge($this->default['attr'], $data['attr']) : $this->default['attr'];
        $data         = array_merge($this->default, $data);
        $html         = '<div class="form-group">';
        $html .= '<label class="' . $data['attr']['label_class'] . ' control-label no-padding-right"> ' . $data['title'] . ' </label>';
        $html .= '<div class="' . $data['attr']['div_class'] . '"><label>';
        $html .= '<input type="checkbox" name="' . $data['name'] . '" class="ace ace-switch ace-switch-' . $data['attr']['style'] . ' btn-' . $data['attr']['btn'] . ' ' . $data['extra_class'] . '" style="' . $data['extra_attr'] . '" ' . ($data['attr']['disabled'] ? ' disabled ' : ' ') . ' id="switch-' . $data['name'] . '"  ' . (($data['value']) ? 'checked' : '') . ' value="1" />';
        $html .= '<span class="lbl" data-lbl="' . $data['attr']['text'][0] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $data['attr']['text'][1] . '"></span></label>';
        $html .= '</div></div>';
        return $html;
    }
}
