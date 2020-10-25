<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

if (!function_exists('make_option')) {

    /**
     * 下拉选择框组件
     * @param array $data 下拉框数据源
     * @param int $selected_id 选择数据ID
     * @param string $show_field 显示名称
     * @param string $val_field 显示值
     * @author 牧羊人
     * @date 2019/4/28
     */
    function make_option($data, $selected_id = 0, $show_field = 'name', $val_field = 'id')
    {
        $html = '';
        $show_field_arr = explode(',', $show_field);
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $show_text = '';
                if (is_array($v)) {
                    foreach ($show_field_arr as $s) {
                        $show_text .= $v[$s] . ' ';
                    }
                    $show_text = substr($show_text, 0, -1);
                    $val_field && $k = $v[$val_field];
                } else {
                    $show_text = $v;
                }
                $sel = '';
                if ($selected_id && $k == $selected_id) {
                    $sel = 'selected';
                }
                $html .= '<option value = ' . $k . ' ' . $sel . '>' . $show_text . '</option>';
            }
        }
        echo $html;
    }
}