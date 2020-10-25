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
 * 表格列表
 * @Author: rainfer <rainfer520@qq.com>
 */
class TableForm
{

    /**
     * 渲染
     *
     * @param array         $fields        字段
     * @param string        $pk            主键
     * @param array         $datas         数据
     * @param array         $right_actions 右侧操作
     * @param string        $page          分页
     * @param boolean|array $order         排序
     * @param boolean|array $delall        有全删
     * @param boolean       $ajax          是否ajax
     *
     * @return string
     */
    public function fetch($fields, $pk = 'id', $datas = [], $right_actions = [], $page = '', $order = false, $delall = false, $ajax = false)
    {
        if ($ajax) {
            $html = '';
        } else {
            $html = '<div class="table-responsive"><form>';
            $html .= '<table class="table table-striped table-bordered table-hover">';
            $html .= '<thead><tr>';
            //是否有全选
            if ($delall) {
                $html .= '<th class="center"><label class="pos-rel">';
                $html .= '<input type="checkbox" class="ace"  id="chkAll" onclick="CheckAll(this.form)" value="全选"/>';
                $html .= '<span class="lbl"></span></label></th>';
            }
            foreach ($fields as $field) {
                if ($right_actions || $field != end($fields)) {
                    $html .= '<th class="' . (isset($field['class']) ? $field['class'] : '') . '">' . $field['title'] . '</th>';
                } else {
                    $html .= '<th class="' . (isset($field['class']) ? $field['class'] : '') . '" style="border-right:#CCC solid 1px;">' . $field['title'] . '</th>';
                }
            }
            //右侧操作
            if ($right_actions) {
                $html .= '<th style="border-right:#CCC solid 1px;">操作</th>';
            }
            $html .= '</tr></thead>';
            $html .= '<tbody id="ajax-data">';
        }
        if ($datas) {
            //表格内容
            foreach ($datas as $data) {
                $html .= '<tr>';
                //是否有全选
                if ($delall) {
                    $html .= '<td align="center"><label class="pos-rel">';
                    $html .= '<input name="ids[]" class="ace check-all" type="checkbox" value="' . (isset($data[$pk]) ? $data[$pk] : '') . '">';
                    $html .= '<span class="lbl"></span></label></td>';
                }
                foreach ($fields as $field) {
                    $type = isset($field['type']) ? $field['type'] : 'text';
                    if (strpos($field['field'], '.')) {
                        $arrs = explode('.', $field['field']);
                        $vv   = $data;
                        foreach ($arrs as $key => $val) {
                            if (isset($vv[$val])) {
                                $vv = $vv[$val];
                            } else {
                                $vv = '';
                                break;
                            }
                        }
                    } else {
                        $vv = isset($data[$field['field']]) ? $data[$field['field']] : '';
                    }
                    $vv = (isset($field['default']) && empty($vv)) ? $field['default'] : $vv;
                    switch ($type) {
                        case 'datetime':
                            $html .= '<td class="' . (isset($field['class']) ? $field['class'] : '') . '">' . date('Y-m-d H:i:s', $vv) . '</td>';
                            break;
                        case 'date':
                            $html .= '<td class="' . (isset($field['class']) ? $field['class'] : '') . '">' . date('Y-m-d', $vv) . '</td>';
                            break;
                        case 'html':
                            $html .= '<td class="' . (isset($field['class']) ? $field['class'] : '') . '">' . $vv . '</td>';
                            break;
                        case 'array':
                            if ($field['array'] && isset($field['array'][$vv])) {
                                $html .= '<td class="' . (isset($field['class']) ? $field['class'] : '') . '">' . $field['array'][$vv] . '</td>';
                            } else {
                                $html .= '<td class="' . (isset($field['class']) ? $field['class'] : '') . '">' . htmlspecialchars($vv) . '</td>';
                            }
                            break;
                        case 'text':
                            $max_length = isset($field['max_length']) ? intval($field['max_length']) : 0;
                            if ($max_length) {
                                $html .= '<td class="' . (isset($field['class']) ? $field['class'] : '') . '">' . html_trim(htmlspecialchars($vv), $max_length) . '</td>';
                            } else {
                                $html .= '<td class="' . (isset($field['class']) ? $field['class'] : '') . '">' . htmlspecialchars($vv) . '</td>';
                            }
                            break;
                        case 'number':
                            $html .= '<td class="' . (isset($field['class']) ? $field['class'] : '') . '">' . htmlspecialchars($vv) . '</td>';
                            break;
                        case 'switch':
                            $options = isset($field['options']) ? $field['options'] : [0 => '禁用', 1 => '启用'];
                            $vv      = $vv ? 1 : 0;
                            $str     = $options[$vv];
                            if ($vv) {
                                $html .= '<td class="' . (isset($field['class']) ? $field['class'] : '') . '"><a class="red open-btn" href="' . $field['url'] . '" data-id="' . $data[$pk] . '" title="' . $str . '"><div><button class="btn btn-minier btn-yellow">' . $str . '</button></div></a></td>';
                            } else {
                                $html .= '<td class="' . (isset($field['class']) ? $field['class'] : '') . '"><a class="red open-btn" href="' . $field['url'] . '" data-id="' . $data[$pk] . '" title="' . $str . '"><div><button class="btn btn-minier btn-danger">' . $str . '</button></div></a></td>';
                            }
                            break;
                        case 'input':
                            $html .= '<td class="' . (isset($field['class']) ? $field['class'] : '') . '">';
                            isset($data[$pk]) && $html .= '<input name="' . $data[$pk] . '" value="' . $vv . '" class="table-input center"/>';
                            $html .= '</td>';
                            break;
                    }
                }
                //右侧操作
                if ($right_actions) {
                    $html .= '<td><div class="hidden-sm hidden-xs action-buttons">';
                    foreach ($right_actions as $key => $value) {
                        switch ($key) {
                            case 'edit':
                                if (is_string($value) || !isset($value['is_pop']) || empty($value['is_pop'])) {
                                    $html .= '<a class="green edit-btn"  href="' . $value . '" data-id="' . $data[$pk] . '" title="修改"><i class="ace-icon fa fa-pencil bigger-130"></i></a>';
                                } else {
                                    $html .= '<a class="green yf-modal-open"  href="javascript:;" data-title="修改" data-url="' . (isset($value['href']) ? $value['href'] : '') . '" data-id="' . $data[$pk] . '" title="修改"><i class="ace-icon fa fa-pencil bigger-130"></i></a>';
                                }
                                break;
                            case 'delete':
                                $html .= '<a class="red confirm-rst-url-btn"  data-info="你确定要删除吗？" href="' . $value . '" data-id="' . $data[$pk] . '" title="删除"><i class="ace-icon fa fa-trash-o bigger-130"></i></a>';
                                break;
                            default:
                                //自定义
                                if (isset($value['condition'])) {
                                    $is_ture = false;
                                    @list($condition_field, $condition_exp, $condition_value) = $value['condition'];
                                    $data_value = isset($data[$condition_field]) ? $data[$condition_field] : false;
                                    if ($condition_value !== false) {
                                        switch ($condition_exp) {
                                            case '=':
                                                $is_ture = ($data_value == $condition_value) ? true : false;
                                                break;
                                            case '>=':
                                                $is_ture = ($data_value >= $condition_value) ? true : false;
                                                break;
                                            case '<=':
                                                $is_ture = ($data_value <= $condition_value) ? true : false;
                                                break;
                                            case '!=':
                                                $is_ture = ($data_value != $condition_value) ? true : false;
                                                break;
                                            case '>':
                                                $is_ture = ($data_value > $condition_value) ? true : false;
                                                break;
                                            case '<':
                                                $is_ture = ($data_value < $condition_value) ? true : false;
                                                break;
                                            default:
                                                $is_ture = false;
                                                break;
                                        }
                                    }
                                    $value_ = $is_ture ? $value['true'] : $value['false'];
                                    if (isset($value_['is_pop']) && $value_['is_pop']) {
                                        $html .= '<a class="' . (isset($value_['class']) ? $value_['class'] : 'green') . ' yf-modal-open" href="javascript:;" data-url="' . (isset($value_['href']) ? $value_['href'] : (isset($value_['field']) ? $data[$value_['field']] : '#')) . '" data-title="' . (isset($value_['title']) ? $value_['title'] : '') . '" target="' . (isset($value_['target']) ? $value_['target'] : '_self') . '" ' . (isset($value_['extra_attr']) ? $value_['extra_attr'] : '') . '><i class="ace-icon bigger-130 ' . (isset($value_['icon']) ? $value_['icon'] : '') . '"></i></a>';
                                    } else {
                                        $html .= '<a class="' . (isset($value_['class']) ? $value_['class'] : 'green') . '" href="' . (isset($value_['href']) ? $value_['href'] : (isset($value_['field']) ? $data[$value_['field']] : '#')) . '" title="' . (isset($value_['title']) ? $value_['title'] : '') . '" target="' . (isset($value_['target']) ? $value_['target'] : '_self') . '" ' . (isset($value_['extra_attr']) ? $value_['extra_attr'] : '') . '><i class="ace-icon bigger-130 ' . (isset($value_['icon']) ? $value_['icon'] : '') . '"></i></a>';
                                    }
                                } else {
                                    if (isset($value['is_pop']) && $value['is_pop']) {
                                        $html .= '<a class="' . (isset($value['class']) ? $value['class'] : 'green') . ' yf-modal-open" href="javascript:;"  data-url="' . (isset($value['href']) ? $value['href'] : (isset($value['field']) ? $data[$value['field']] : '#')) . '" data-title="' . (isset($value['title']) ? $value['title'] : '') . '" target="' . (isset($value['target']) ? $value['target'] : '_self') . '" ' . (isset($value['extra_attr']) ? $value['extra_attr'] : '') . '><i class="ace-icon bigger-130 ' . (isset($value['icon']) ? $value['icon'] : '') . '"></i></a>';
                                    } else {
                                        $html .= '<a class="' . (isset($value['class']) ? $value['class'] : 'green') . '" href="' . (isset($value['href']) ? $value['href'] : (isset($value['field']) ? $data[$value['field']] : '#')) . '" title="' . (isset($value['title']) ? $value['title'] : '') . '" target="' . (isset($value['target']) ? $value['target'] : '_self') . '" ' . (isset($value['extra_attr']) ? $value['extra_attr'] : '') . '><i class="ace-icon bigger-130 ' . (isset($value['icon']) ? $value['icon'] : '') . '"></i></a>';
                                    }
                                }
                                break;
                        }
                    }
                    $html .= '</div><div class="hidden-md hidden-lg"><div class="inline position-relative">';
                    $html .= '<button class="btn btn-minier btn-primary dropdown-toggle" data-toggle="dropdown" data-position="auto"><i class="ace-icon fa fa-cog icon-only bigger-110"></i></button>';
                    $html .= '<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">';
                    foreach ($right_actions as $key => $value) {
                        switch ($key) {
                            case 'edit':
                                $html .= '<li>';
                                if (is_string($value) || !isset($value['is_pop']) || empty($value['is_pop'])) {
                                    $html .= '<a href="' . $value . '" data-id="' . $data[$pk] . '" class="tooltip-success" data-rel="tooltip" title="" data-original-title="修改"><span class="green"><i class="ace-icon fa fa-pencil-square-o bigger-120"></i></span></a>';
                                } else {
                                    $html .= '<a href="javascript:;" data-title="修改" data-url="' . (isset($value['href']) ? $value['href'] : '') . '" data-id="' . $data[$pk] . '" class="tooltip-success edit-btn yf-modal-open" data-rel="tooltip" title="" data-original-title="修改"><span class="green"><i class="ace-icon fa fa-pencil-square-o bigger-120"></i></span></a>';
                                }
                                $html .= '</li>';
                                break;
                            case 'delete':
                                $html .= '<li><a href="' . $value . '" data-id="' . $data[$pk] . '" class="tooltip-error confirm-rst-url-btn" data-rel="tooltip" title="" data-info="你确定要删除吗？" data-original-title="删除"><span class="red"><i class="ace-icon fa fa-trash-o bigger-120"></i></span></a></li>';
                                break;
                            default:
                                //自定义
                                if (isset($value['condition'])) {
                                    $is_ture = false;
                                    @list($condition_field, $condition_exp, $condition_value) = $value['condition'];
                                    $data_value = isset($data[$condition_field]) ? $data[$condition_field] : false;
                                    if ($condition_value !== false) {
                                        switch ($condition_exp) {
                                            case '=':
                                                $is_ture = ($data_value == $condition_value) ? true : false;
                                                break;
                                            case '>=':
                                                $is_ture = ($data_value >= $condition_value) ? true : false;
                                                break;
                                            case '<=':
                                                $is_ture = ($data_value <= $condition_value) ? true : false;
                                                break;
                                            case '!=':
                                                $is_ture = ($data_value != $condition_value) ? true : false;
                                                break;
                                            case '>':
                                                $is_ture = ($data_value > $condition_value) ? true : false;
                                                break;
                                            case '<':
                                                $is_ture = ($data_value < $condition_value) ? true : false;
                                                break;
                                            default:
                                                $is_ture = false;
                                                break;
                                        }
                                    }
                                    $value_ = $is_ture ? $value['true'] : $value['false'];
                                    if (isset($value_['is_pop']) && $value_['is_pop']) {
                                        $html .= '<li><a class="' . (isset($value_['class']) ? $value_['class'] : 'green') . ' yf-modal-open" href="javascript:;" data-url="' . (isset($value_['href']) ? $value_['href'] : (isset($value_['field']) ? $data[$value_['field']] : '#')) . '" data-title="' . (isset($value_['title']) ? $value_['title'] : '') . '" target="' . (isset($value_['target']) ? $value_['target'] : '_self') . '" ' . (isset($value_['extra_attr']) ? $value_['extra_attr'] : '') . '><i class="ace-icon bigger-130 ' . (isset($value_['icon']) ? $value_['icon'] : '') . '"></i></a></li>';
                                    } else {
                                        $html .= '<li><a class="' . (isset($value_['class']) ? $value_['class'] : 'green') . '" href="' . (isset($value_['href']) ? $value_['href'] : (isset($value_['field']) ? $data[$value_['field']] : '#')) . '" title="' . (isset($value_['title']) ? $value_['title'] : '') . '" target="' . (isset($value_['target']) ? $value_['target'] : '_self') . '" ' . (isset($value_['extra_attr']) ? $value_['extra_attr'] : '') . '><i class="ace-icon bigger-130 ' . (isset($value_['icon']) ? $value_['icon'] : '') . '"></i></a></li>';
                                    }
                                } else {
                                    if (isset($value['is_pop']) && $value['is_pop']) {
                                        $html .= '<li><a class="' . (isset($value['class']) ? $value['class'] : 'green') . ' yf-modal-open" href="javascript:;"  data-url="' . (isset($value['href']) ? $value['href'] : (isset($value['field']) ? $data[$value['field']] : '#')) . '" data-title="' . (isset($value['title']) ? $value['title'] : '') . '" target="' . (isset($value['target']) ? $value['target'] : '_self') . '" ' . (isset($value['extra_attr']) ? $value['extra_attr'] : '') . '><i class="ace-icon bigger-130 ' . (isset($value['icon']) ? $value['icon'] : '') . '"></i></a></li>';
                                    } else {
                                        $html .= '<li><a class="' . (isset($value['class']) ? $value['class'] : 'green') . '" href="' . (isset($value['href']) ? $value['href'] : (isset($value['field']) ? $data[$value['field']] : '#')) . '" title="' . (isset($value['title']) ? $value['title'] : '') . '" target="' . (isset($value['target']) ? $value['target'] : '_self') . '" ' . (isset($value['extra_attr']) ? $value['extra_attr'] : '') . '><i class="ace-icon bigger-130 ' . (isset($value['icon']) ? $value['icon'] : '') . '"></i></a></li>';
                                    }
                                }
                                break;
                        }
                    }
                    $html .= '</ul></div></div></td>';
                }
                $html .= '</tr>';
            }
        }
        //页码
        $html .= '<tr>';
        if ($delall) {
            $html .= '<td height="50" align="center"><button data-url="' . $delall . '" class="btn btn-white btn-yellow btn-sm delall-btn">全删</button></td>';
        }
        if ($order) {
            $html .= '<td height="50" align=""><button data-url="' . $order . '" class="btn btn-white btn-yellow btn-sm order-btn">排序</button></td>';
        }
        $html .= '<td height="50" colspan="20" align="right">' . $page . '</td>';
        $html .= '</tr>';
        if ($ajax) {
            return $html;
        } else {
            $html .= '</tbody></table></form></div>';
            return $html;
        }
    }
}
