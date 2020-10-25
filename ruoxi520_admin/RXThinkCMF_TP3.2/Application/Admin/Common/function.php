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

/**
 * 基础分页的相同代码封装，使前台的代码更少
 * @param $count 要分页的总记录数
 * @param number $pagesize 每页查询条数
 * @return \Think\Page
 */
function getPage($count, $pagesize = 10) {
    $p = new Think\Page($count, $pagesize);
    $p->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录 第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
    $p->setConfig('prev', '上一页');
    $p->setConfig('next', '下一页');
    $p->setConfig('last', '末页');
    $p->setConfig('first', '首页');
    $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $p->lastSuffix = false;//最后一页不显示为总页数
    return $p;
}


/**
 * 下拉框组件
 * 
 * @author 牧羊人
 * @date 2018-07-10
 * @param unknown $arr
 * @param string $selected
 * @param string $show_field 支持多个字段显示 格式field_a,field_b
 * @param string $val_field
 * @return string
 */
function make_option($arr, $selected='', $show_field='', $val_field='') {
    $ret = '';
    $show_field_arr = explode(',', $show_field);
    if (is_array( $arr )) {
        foreach ($arr as $k => $v) {
            $show_text = '';
            if (is_array( $v )) {
                foreach ($show_field_arr as $s) {
                    $show_text .= $v[$s].' ';
                }
                $show_text = substr($show_text, 0, -1);
                $val_field && $k = $v[$val_field];
            } else {
                $show_text = $v;
            }
            $sel = '';
            if ($selected && $k == $selected) {
                $sel = 'selected';
            }
            $ret .= '<option value =' . $k . ' ' . $sel . '>' . $show_text . '</option>';
        }
    }
    return $ret;
}

/**
 * 单选按钮组件
 * 
 * @author 牧羊人
 * @date 2018-10-22
 * @param unknown $arr
 * @param unknown $name
 * @param string $checked
 * @param unknown $val
 * @param unknown $field
 * @param number $show_num
 * @return string
 */
function make_radio($arr, $name, $checked='', $val, $field, $show_num=10) {
    $ret = '';
    $m = 1;
    if (is_array( $arr )) {
        foreach ($arr as $k => $v) {
            $show_name  = $name;
            $show_id    = $name.'_'.$k;
            if (is_array( $v )) {
                $show_val   = $v[$val];
                $show_field = $v[$field];
                $k 			= $show_val;
            } else {
                $show_val   = $k;
                $show_field = $v;
            }
            $sel = '';
            if ( $k == $checked) {
                $sel = 'checked="checked"';
            }
            $ret .= '<input name="'.$show_name.'" id="'.$show_id.'" value="'.$show_val.'" title="'.$show_field.'" '.$sel.' type="radio">';
            if ($m % $show_num == 0) {
                $ret .= '<br>';
            }
            $m ++;
        }
    }
    return $ret;
}

/**
 * 生成复选框checkbox
 * 
 * @author 牧羊人
 * @date 2018-10-24
 * @param unknown $arr
 * @param unknown $name
 * @param unknown $checked_array
 * @param number $per_line
 * @param string $value_field
 * @param string $text_field
 * @param string $class
 * @return string
 */
function make_checkbox($arr, $name, $checkedArr=array(), $val, $fleid,$class='') {
    $result = '';
    foreach ($arr as $k=>$v) {
        if (is_array($v)) {
            $show_val = $v[$val];
            $show_field = $v[$fleid];
        } else {
            $show_val = $k;
            $show_field = $v;
        }
        $checked = '';
        if ($checkedArr && in_array($show_val, $checkedArr)) {
            $checked = 'checked';
        }
        $result .= '<input class="'.$class.'" name="'.$name.'[]" lay-skin="primary" title="'.$show_field.'" value="'.$show_val.'" '.$checked.' type="checkbox">';
    }
    return $result;
}
