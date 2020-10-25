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
 * tab分组
 * @Author: rainfer <rainfer520@qq.com>
 */
class GroupForm
{
    protected $default = [
        'id'               => '',
        'position'         => '',
        'color'            => '',
        'tab_space'        => 0,
        'tab_padding'      => 0,
        'content_padding'  => 0,
        'content_noborder' => false,
        'has_active'       => false,
        'groups'           => []
    ];

    /**
     * 渲染
     *
     * @param array   $groups           分组数据
     * @param string  $id               tabs的id
     * @param string  $position         tab的位置 //tabs-below tabs-left tabs-right
     * @param string  $color            tab的颜色  ''或blue
     * @param int     $tab_space        tab的间距 0-4
     * @param int     $tab_padding      tab的左间距 0 2 4 ...32
     * @param int     $content_padding  内容的内间距 0 2 4 ...32
     * @param boolean $content_noborder 内容的边框
     *
     * @return string
     */
    public function fetch($groups = [], $id = '', $position = '', $color = '', $tab_space = 0, $tab_padding = 0, $content_padding = 0, $content_noborder = false)
    {
        $data = [
            'id'               => $id,//id
            'position'         => $position,
            'color'            => $color,
            'tab_space'        => $tab_space,
            'tab_padding'      => $tab_padding,
            'content_padding'  => $content_padding,
            'content_noborder' => $content_noborder,
            'groups'           => $groups//分组数据
        ];
        $data = array_merge($this->default, $data);
        $html = '<div class="row"><div class="col-xs-12">';
        $html .= '<div class="tabbable ' . $data['position'] . '">';
        if ($data['position'] == 'tabs-below') {
            $html .= '<div class="tab-content ' . ((isset($data['content_noborder']) && $data['content_noborder']) ? 'no-border' : '') . '">';
            if ($data['groups']) {
                foreach ($data['groups'] as $key => $group) {
                    if (isset($group['dropdown']) && $group['dropdown']) {
                        foreach ($group['dropdown'] as $kk => $vv) {
                            $active = (isset($vv['is_active']) && $vv['is_active'] && (isset($group['is_active']) && $group['is_active'])) ? 'in active' : '';
                            $href   = (isset($vv['href']) && $vv['href']) ? $vv['href'] : ('dropdown_' . $key . '_' . $kk);
                            $html .= '<div id="' . $href . '" class="tab-pane fade ' . $active . '">';
                            if (isset($vv['form_url']) && $vv['form_url']) {
                                $html .= '<form name="' . (isset($vv['form_name']) ? $vv['form_name'] : '') . '" class="form-horizontal ' . (isset($vv['form_class']) ? $vv['form_class'] : '') . ' " method="post" action="' . $vv['form_url'] . '">';
                            }
                            $html .= (isset($vv['html']) && $vv['html']) ? $vv['html'] : '';
                            if (isset($vv['form_url']) && $vv['form_url']) {
                                //按钮
                                $html .= '<div class="clearfix form-actions">';
                                $html .= '<div class="col-md-offset-3 col-md-9">';
                                $html .= '<button class="btn btn-info" type="submit"><i class="ace-icon fa fa-check bigger-110"></i>确定</button>';
                                $html .= '<button class="btn" type="reset"><i class="ace-icon fa fa-undo bigger-110"></i>重置</button>';
                                $html .= '</div></div></form>';
                            }
                            $html .= '</div>';
                        }
                    } else {
                        $active = (isset($group['is_active']) && $group['is_active']) ? 'in active' : '';
                        $html .= '<div id="' . ((isset($group['href']) && $group['href']) ? $group['href'] : ('tab' . $key)) . '" class="tab-pane fade ' . $active . '">';
                        if (isset($group['form_url']) && $group['form_url']) {
                            $html .= '<form name="' . (isset($group['form_name']) ? $group['form_name'] : '') . '" class="form-horizontal ' . (isset($group['form_class']) ? $group['form_class'] : '') . ' " method="post" action="' . $group['form_url'] . '">';
                        }
                        $html .= (isset($group['html']) && $group['html']) ? $group['html'] : '';
                        if (isset($group['form_url']) && $group['form_url']) {
                            //按钮
                            $html .= '<div class="clearfix form-actions">';
                            $html .= '<div class="col-md-offset-3 col-md-9">';
                            $html .= '<button class="btn btn-info" type="submit"><i class="ace-icon fa fa-check bigger-110"></i>确定</button>';
                            $html .= '<button class="btn" type="reset"><i class="ace-icon fa fa-undo bigger-110"></i>重置</button>';
                            $html .= '</div></div></form>';
                        }
                        $html .= '</div>';
                    }
                }
            }
            $html .= '</div>';
            //tab按钮
            $data['id']          = $data['id'] ?: 'mytab';
            $data['color']       = ($data['color'] == 'blue') ? ' tab-color-blue background-blue ' : '';
            $data['tab_space']   = $data['tab_space'] ? ('tab-space-' . $data['tab_space']) : '';
            $data['tab_padding'] = $data['tab_padding'] ? ('padding-' . $data['tab_padding']) : '';
            $html .= '<ul class="nav nav-tabs ' . $data['color'] . ' ' . $data['tab_space'] . ' ' . $data['tab_padding'] . '" id="' . $data['id'] . '">';
            if ($data['groups']) {
                foreach ($data['groups'] as $key => $group) {
                    $active = (isset($group['is_active']) && $group['is_active']) ? 'active' : '';
                    $html .= '<li class="' . $active . ' ' . ((isset($group['dropdown']) && $group['dropdown']) ? 'dropdown' : '') . '">';
                    if (isset($group['dropdown']) && $group['dropdown']) {
                        $html .= '<a data-toggle="dropdown" class="dropdown-toggle" href="#">';
                        $html .= (isset($group['title']) && $group['title']) ? $group['title'] : ('tab_title' . $key);
                        $html .= '<i class="ace-icon fa fa-caret-down bigger-110 width-auto"></i></a><ul class="dropdown-menu dropdown-info">';
                        foreach ($group['dropdown'] as $kk => $vv) {
                            $active = (isset($vv['is_active']) && $vv['is_active'] && (isset($group['is_active']) && $group['is_active'])) ? 'active' : '';
                            $html .= '<li class="' . $active . '">';
                            $href  = (isset($vv['href']) && $vv['href']) ? $vv['href'] : ('dropdown_' . $key . '_' . $kk);
                            $title = (isset($vv['title']) && $vv['title']) ? $vv['title'] : ('dropdown_' . $key . '_' . $kk);
                            $html .= '<a data-toggle="tab" href="#' . $href . '">' . $title . '</a>';
                            $html .= '</li>';
                        }
                        $html .= '</ul></li>';
                    } else {
                        $html .= '<a data-toggle="tab" href="#' . ((isset($group['href']) && $group['href']) ? $group['href'] : ('tab' . $key)) . '">';
                        if (isset($group['attr_left']) && $group['attr_left']) {
                            $html .= $group['attr_left'];
                        }
                        $html .= (isset($group['title']) && $group['title']) ? $group['title'] : ('tab_title' . $key);
                        if (isset($group['attr_right']) && $group['attr_right']) {
                            $html .= $group['attr_right'];
                        }
                        $html .= '</a></li>';
                    }
                }
            }
            $html .= '</ul>';
        } else {
            //tab按钮
            $data['id']          = $data['id'] ?: 'mytab';
            $data['color']       = ($data['color'] == 'blue') ? ' tab-color-blue background-blue ' : '';
            $data['tab_space']   = $data['tab_space'] ? ('tab-space-' . $data['tab_space']) : '';
            $data['tab_padding'] = $data['tab_padding'] ? ('padding-' . $data['tab_padding']) : '';
            $html .= '<ul class="nav nav-tabs ' . $data['color'] . ' ' . $data['tab_space'] . ' ' . $data['tab_padding'] . '" id="' . $data['id'] . '">';
            if ($data['groups']) {
                foreach ($data['groups'] as $key => $group) {
                    $active = (isset($group['is_active']) && $group['is_active']) ? 'active' : '';
                    $html .= '<li class="' . $active . ' ' . ((isset($group['dropdown']) && $group['dropdown']) ? 'dropdown' : '') . '">';
                    if (isset($group['dropdown']) && $group['dropdown']) {
                        $html .= '<a data-toggle="dropdown" class="dropdown-toggle" href="#">';
                        $html .= (isset($group['title']) && $group['title']) ? $group['title'] : ('tab_title' . $key);
                        $html .= '<i class="ace-icon fa fa-caret-down bigger-110 width-auto"></i></a><ul class="dropdown-menu dropdown-info">';
                        foreach ($group['dropdown'] as $kk => $vv) {
                            $active = (isset($vv['is_active']) && $vv['is_active'] && (isset($group['is_active']) && $group['is_active'])) ? 'active' : '';
                            $html .= '<li class="' . $active . '">';
                            $href  = (isset($vv['href']) && $vv['href']) ? $vv['href'] : ('dropdown_' . $key . '_' . $kk);
                            $title = (isset($vv['title']) && $vv['title']) ? $vv['title'] : ('dropdown_' . $key . '_' . $kk);
                            $html .= '<a data-toggle="tab" href="#' . $href . '">' . $title . '</a>';
                            $html .= '</li>';
                        }
                        $html .= '</ul></li>';
                    } else {
                        $html .= '<a data-toggle="tab" href="#' . ((isset($group['href']) && $group['href']) ? $group['href'] : ('tab' . $key)) . '">';
                        if (isset($group['attr_left']) && $group['attr_left']) {
                            $html .= $group['attr_left'];
                        }
                        $html .= (isset($group['title']) && $group['title']) ? $group['title'] : ('tab_title' . $key);
                        if (isset($group['attr_right']) && $group['attr_right']) {
                            $html .= $group['attr_right'];
                        }
                        $html .= '</a></li>';
                    }
                }
            }
            $html .= '</ul>';
            $html .= '<div class="tab-content ' . ((isset($data['content_noborder']) && $data['content_noborder']) ? 'no-border' : '') . '">';
            if ($data['groups']) {
                foreach ($data['groups'] as $key => $group) {
                    if (isset($group['dropdown']) && $group['dropdown']) {
                        foreach ($group['dropdown'] as $kk => $vv) {
                            $active = (isset($vv['is_active']) && $vv['is_active'] && (isset($group['is_active']) && $group['is_active'])) ? 'in active' : '';
                            $href   = (isset($vv['href']) && $vv['href']) ? $vv['href'] : ('dropdown_' . $key . '_' . $kk);
                            $html .= '<div id="' . $href . '" class="tab-pane fade ' . $active . '">';
                            if (isset($vv['form_url']) && $vv['form_url']) {
                                $html .= '<form name="' . (isset($vv['form_name']) ? $vv['form_name'] : '') . '" class="form-horizontal ' . (isset($vv['form_class']) ? $vv['form_class'] : '') . ' " method="post" action="' . $vv['form_url'] . '">';
                            }
                            $html .= (isset($vv['html']) && $vv['html']) ? $vv['html'] : '';
                            if (isset($vv['form_url']) && $vv['form_url']) {
                                //按钮
                                $html .= '<div class="clearfix form-actions">';
                                $html .= '<div class="col-md-offset-3 col-md-9">';
                                $html .= '<button class="btn btn-info" type="submit"><i class="ace-icon fa fa-check bigger-110"></i>确定</button>';
                                $html .= '<button class="btn" type="reset"><i class="ace-icon fa fa-undo bigger-110"></i>重置</button>';
                                $html .= '</div></div></form>';
                            }
                            $html .= '</div>';
                        }
                    } else {
                        $active = (isset($group['is_active']) && $group['is_active']) ? 'in active' : '';
                        $html .= '<div id="' . ((isset($group['href']) && $group['href']) ? $group['href'] : ('tab' . $key)) . '" class="tab-pane fade ' . $active . '">';
                        if (isset($group['form_url']) && $group['form_url']) {
                            $html .= '<form name="' . (isset($group['form_name']) ? $group['form_name'] : '') . '" class="form-horizontal ' . (isset($group['form_class']) ? $group['form_class'] : '') . ' " method="post" action="' . $group['form_url'] . '">';
                        }
                        $html .= (isset($group['html']) && $group['html']) ? $group['html'] : '';
                        if (isset($group['form_url']) && $group['form_url']) {
                            //按钮
                            $html .= '<div class="clearfix form-actions">';
                            $html .= '<div class="col-md-offset-3 col-md-9">';
                            $html .= '<button class="btn btn-info" type="submit"><i class="ace-icon fa fa-check bigger-110"></i>确定</button>';
                            $html .= '<button class="btn" type="reset"><i class="ace-icon fa fa-undo bigger-110"></i>重置</button>';
                            $html .= '</div></div></form>';
                        }
                        $html .= '</div>';
                    }
                }
            }
            $html .= '</div>';
        }
        $html .= '</div></div></div>';
        return $html;
    }
}
