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
 * 下拉按钮
 * @Author: rainfer <rainfer520@qq.com>
 */
class ButtonDropdownForm
{
    protected $default = [
        'id'         => '',//id
        'title'      => '',//标签
        'is_divider' => false,//是否分隔线
        'attr'       => [
            //应用按钮 btn-app 圆角默认12px
            //颜色 btn-default btn-primary btn-info btn-success btn-warning btn-danger btn-inverse btn-pink btn-purple btn-yellow btn-grey btn-light btn-link btn-white
            //圆角 radius-4 no-radius
            //尺寸 btn-lg '' btn-sm btn-xs btn-minier
            //边框 no-border '' btn-bold btn-round
            //块按钮 btn-block ''
            'class'    => 'btn btn-primary',
            //'' 'submit' 'reset' 'back'
            'type'     => '',
            'icon_l'   => '',
            'icon_r'   => '',
            //是否只读
            'disabled' => false,
            //提示
            'tips'     => '',
            //按钮后面的标签badge ['title'=>'','class'=>'']
            'span'     => [],
            'href'     => '',
            'target'   => '_self',
            //附加属性
            'data'     => []
        ]
    ];

    /**
     * 渲染
     *
     * @param array  $button    顶部按钮 元素参数见button,须为关联数组
     * @param array  $groups    每个元素参数见button,须为关联数组
     * @param bool   $has_drbtn 是否单独下拉按钮
     * @param string $class     dropup 上拉显示 ''下拉
     * @param string $dr_class  下拉class dropdown-menu-right
     *                          //颜色 dropdown-default dropdown-danger ...
     *                          //下拉菜单方向 dropdown-menu-right
     *
     * @return string
     */
    public function fetch($button = [], $groups = [], $has_drbtn = false, $class = '', $dr_class = '')
    {
        $datas = [
            'button'    => $button,
            'has_drbtn' => $has_drbtn,
            'class'     => $class,
            'dr_class'  => $dr_class,
            'groups'    => $groups
        ];
        $html  = '<div class="btn-group ' . $datas['class'] . '">';
        //第一个按钮
        $button         = $datas['button'];
        $button['attr'] = isset($button['attr']) ? array_merge($this->default['attr'], $button['attr']) : $this->default['attr'];
        $button         = array_merge($this->default, $button);
        $button_str     = '';
        if (isset($button['attr']['data']) && $button['attr']['data']) {
            foreach ($button['attr']['data'] as $k => $v) {
                $button_str .= 'data-' . $k . '="' . $v . '" ';
            }
        }
        if ($datas['has_drbtn']) {
            $html .= '<button ' . ($button['id'] ? 'id="' . $button['id'] . '"' : '') . ' class="' . $button['attr']['class'] . '" ' . $button_str . ' ' . ($button['attr']['tips'] ? 'data-rel="tooltip" data-placement="bottom" data-original-title="' . $button['attr']['tips'] . '" ' : '') . '>';
        } else {
            $html .= '<button data-toggle="dropdown" ' . ($button['id'] ? 'id="' . $button['id'] . '"' : '') . ' class="dropdown-toggle ' . $button['attr']['class'] . '" ' . $button_str . ' ' . ($button['attr']['tips'] ? 'data-rel="tooltip" data-placement="bottom" data-original-title="' . $button['attr']['tips'] . '" ' : '') . '>';
        }
        if ($button['attr']['icon_l']) {
            $html .= '<i class="' . $button['attr']['icon_l'] . '"></i>';
        }
        $html .= $button['title'];
        if ($button['attr']['span']) {
            $html .= '<span class="' . $button['attr']['span']['class'] . '">' . $button['attr']['span']['title'] . '</span>';
        }
        if ($button['attr']['icon_r']) {
            $html .= '<i class="' . $button['attr']['icon_r'] . '"></i>';
        }
        //下拉按钮
        if ($datas['has_drbtn']) {
            $html .= '</button>';
            $html .= '<button data-toggle="dropdown" class="btn dropdown-toggle">';
            $html .= '<span class="ace-icon fa fa-caret-down icon-only"></span>';
            $html .= '</button>';
        } else {
            $html .= '<span class="ace-icon fa fa-caret-down icon-on-right"></span></button>';
        }
        //下拉按钮组数据
        $html .= '<ul class="dropdown-menu ' . $datas['dr_class'] . '">';
        if ($datas['groups'] && is_array($datas['groups'])) {
            foreach ($datas['groups'] as $data) {
                $data['attr'] = isset($data['attr']) ? array_merge($this->default['attr'], $data['attr']) : $this->default['attr'];
                $data         = array_merge($this->default, $data);
                if ($data['is_divider']) {
                    $html .= '<li class="divider"></li>';
                } else {
                    //处理$data['attr']['data']
                    $data_str = '';
                    if (isset($data['attr']['data']) && $data['attr']['data']) {
                        foreach ($data['attr']['data'] as $k => $v) {
                            $data_str .= 'data-' . $k . '="' . $v . '" ';
                        }
                    }
                    $html .= '<li><a ' . ($data['id'] ? 'id="' . $data['id'] . '"' : '') . ' href="' . $data['attr']['href'] . '" ' . ($data['attr']['disabled'] ? 'disabled' : '') . '" target="' . $data['attr']['target'] . '" ' . $data_str . ' ' . ($data['attr']['tips'] ? 'data-rel="tooltip" data-placement="bottom" data-original-title="' . $data['attr']['tips'] . '" ' : '') . '>';
                    if ($data['attr']['icon_l']) {
                        $html .= '<i class="' . $data['attr']['icon_l'] . '"></i>';
                    }
                    $html .= $data['title'];
                    if ($data['attr']['span']) {
                        $html .= '<span class="' . $data['attr']['span']['class'] . '">' . $data['attr']['span']['title'] . '</span>';
                    }
                    if ($data['attr']['icon_r']) {
                        $html .= '<i class="' . $data['attr']['icon_r'] . '"></i>';
                    }
                    $html .= '</a></li>';
                }
            }
        }
        $html .= '</ul></div>';
        return $html;
    }
}
