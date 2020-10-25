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
 * 验证码表单
 * @Author: rainfer <rainfer520@qq.com>
 */
class CaptchaForm
{
    protected $default = [
        'name'        => '',//name
        'id'          => '',//id
        'title'       => '',//标签
        'attr'        => [
            'placeholder' => '输入验证码',//预设值
            'tips'        => '',//提示
            'label_class'  => 'col-sm-3',//标签class
            'div_class' => 'col-sm-9',//标签class
        ],//属性
        'extra_attr_input'  => '',//input的额外属性
        'extra_class_input' => 'col-xs-10 col-sm-5',//input的class
        'extra_css_input'  => '',//input的css
        'extra_attr_img'  => '',//img的额外属性
        'extra_class_img' => 'col-xs-10 col-sm-5',//img的class
        'extra_css_img'  => ''//img的css
    ];

    /**
     * 渲染
     *
     * @param string $name        验证码名
     * @param string $id          验证码标识id
     * @param string $title       标题
     * @param array  $attr        属性
     * @param string $extra_attr_input  input额外属性
     * @param string $extra_class_input input额外css类名
     * @param string $extra_css_input   input额外style
     * @param string $extra_attr_img  img额外属性
     * @param string $extra_class_img img额外css类名
     * @param string $extra_css_img   img额外style
     *
     * @return string
     */
    public function fetch($name, $id = '', $title = '', $attr = [], $extra_attr_input = '', $extra_class_input = 'col-xs-10 col-sm-5', $extra_css_input = '', $extra_attr_img = '', $extra_class_img = 'col-xs-10 col-sm-3', $extra_css_img = 'cursor: pointer;border: 1px solid #d5d5d5;height:34px;margin-left:10px;')
    {
        $data         = [
            'name'        => $name,//name
            'id'          => $id,//id
            'title'       => $title,//标签
            'attr'        => $attr,//属性
            'extra_attr_input'   => $extra_attr_input,//input的额外属性
            'extra_class_input' => $extra_class_input,//input的class
            'extra_css_input'   => $extra_css_input,//input的style
            'extra_attr_img'   => $extra_attr_img,//img的额外属性
            'extra_class_img' => $extra_class_img,//img的class
            'extra_css_img'   => $extra_css_img,//img的style
        ];
        $data['attr'] = isset($data['attr']) ? array_merge($this->default['attr'], $data['attr']) : $this->default['attr'];
        $data         = array_merge($this->default, $data);
        $html         = '<div class="form-group">';
        $html .= '<label class="' . $data['attr']['label_class'] . ' control-label no-padding-right"> ' . $data['title'] . ' </label>';
        $html .= '<div class="'.$data['attr']['div_class'].'">';
        $html .= '<input type="text" name="' . $data['name'] . '" id="captcha-input-' . $data['name'] . '" class="' . $data['extra_class_input'] . '" style="' . $data['extra_css_input'] . '" placeholder="' . $data['attr']['placeholder'] . '" ' . ($data['attr']['tips'] ? 'data-rel="tooltip" data-placement="bottom" data-original-title="' . $data['attr']['tips'] . '" ' : '') . $data['extra_attr_input'] . '" />';
        $html .= '<img class="no-padding ' . $data['extra_class_img'] . '" id="captcha-img-' . $data['name'] . '" src="'. url('verify', ['id'=>$data['id']]) .'" onClick="this.src=\''.url('verify', ['id'=>$data['id']]).'?\'+Math.random()" style="' . $data['extra_css_img'] . '" title="点击获取"' . $data['extra_attr_img'] .'>';
        $html .= '</div></div>';
        return $html;
    }
}
