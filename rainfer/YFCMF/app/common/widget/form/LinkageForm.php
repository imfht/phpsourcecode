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

/**
 * 多级联动表单
 * @Author: rainfer <rainfer520@qq.com>
 */
class LinkageForm
{
    protected $default = [
        'title' => '',//标签
        'data'  => [], //每个元素结构['name'=>'',title'=>'','data'=>'url' or data数组,'id'=>'','value'=>'','default'=>'请选择']
        'attr'  => [
            'label_class' => 'col-sm-3',//标签class
            'div_class'   => 'col-sm-9'//input上层div的class
        ],//属性
    ];

    /**
     * 渲染
     *
     * @param string $title 标签
     * @param array  $data
     * @param array  $attr
     *
     * @return string
     */
    public function fetch($title = '', $data = [], $attr = [])
    {
        $data         = [
            'title' => $title,
            'data'  => $data,
            'attr'  => $attr
        ];
        $data['attr'] = isset($data['attr']) ? array_merge($this->default['attr'], $data['attr']) : $this->default['attr'];
        $data         = array_merge($this->default, $data);
        $html         = '<div class="form-group">';
        $html .= '<label class="' . $data['attr']['label_class'] . ' control-label no-padding-right"> ' . $data['title'] . ' </label>';
        $html .= '<div class="' . $data['attr']['div_class'] . '">';
        if ($data['data']) {
            foreach ($data['data'] as $k => $v) {
                $html .= '<label>' . (isset($v['title']) ? $v['title'] : '') . '</label>';
                $data_url = '';
                $data_id  = '';
                //非最后1个，且url不为空
                if ($k < count($data['data']) - 1 && isset($data['data'][$k + 1]['url']) && is_string($data['data'][$k + 1]['url'])) {
                    $data_url = $data['data'][$k + 1]['url'];
                }
                //非最后1个
                if ($k < count($data['data']) - 1) {
                    $data_id = (isset($data['data'][$k + 1]['id']) && $data['data'][$k + 1]['id']) ? $data['data'][$k + 1]['id'] : ('linkage-' . $data['data'][$k + 1]['name']);
                }
                $html .= '<select name="' . $v['name'] . '" class="margin-r10 linkage" data-url="' . $data_url . '" data-id="' . $data_id . '" id="' . ((isset($v['id']) && $v['id']) ? $v['id'] : ('linkage-' . $v['name'])) . '">';
                if (isset($v['default']) && $v['default']) {
                    $html .= '<option value="" ' . ((!isset($v['value']) || !$v['value']) ? 'selected="selected"' : '') . '>' . $v['default'] . '</option>';
                } elseif (!isset($v['data']) || !$v['data']) {
                    $html .= '<option value="" ' . ((!isset($v['value']) || !$v['value']) ? 'selected="selected"' : '') . '>' . '请选择' . '</option>';
                }
                if (isset($v['data']) && is_array($v['data'])) {
                    foreach ($v['data'] as $kk => $vv) {
                        if (is_assoc($v['data']) || is_numeric($kk)) {
                            $html .= '<option ' . ((isset($v['value']) && $kk == $v['value']) ? 'selected' : '') . ' value="' . $kk . '">' . $vv . '</option>';
                        } else {
                            $html .= '<option ' . ((isset($v['value']) && $vv == $v['value']) ? 'selected' : '') . ' value="' . $vv . '">' . $vv . '</option>';
                        }
                    }
                }
                $html .= '</select>';
            }
        }
        $html .= '</div></div>';
        return $html;
    }
}
