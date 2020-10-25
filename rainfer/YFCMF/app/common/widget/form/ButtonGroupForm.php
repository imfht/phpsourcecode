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
 * 按钮组
 * @Author: rainfer <rainfer520@qq.com>
 */
class ButtonGroupForm
{
    protected $default = [
        'id'    => '',//id
        'title' => '',//标签
        'type'  => '',
        'attr'  => [
            //应用按钮 btn-app 圆角默认12px
            //颜色 btn-default btn-primary btn-info btn-success btn-warning btn-danger btn-inverse btn-pink btn-purple btn-yellow btn-grey btn-light btn-link btn-white
            //圆角 radius-4 no-radius
            //尺寸 btn-lg '' btn-sm btn-xs btn-minier
            //边框 no-border '' btn-bold btn-round
            //块按钮 btn-block ''
            'class'    => 'btn btn-primary',
            //'' 'submit' 'reset'
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
            //是否弹窗方式
            'is_pop'   => false,
            //附加属性
            'data'     => []
        ]
    ];

    /**
     * 渲染
     *
     * @param array  $groups 每个元素参数见button
     * @param string $class  btn-corner2端按钮圆角 btn-group-vertical 垂直按钮组
     *
     * @return string
     */
    public function fetch($groups = [], $class = '')
    {
        $datas = [
            'groups' => $groups,
            'class'  => $class
        ];
        $html  = '';
        if ($datas['groups'] && is_array($datas['groups'])) {
            $html .= '<div class="btn-group ' . $datas['class'] . '">';
            foreach ($datas['groups'] as $data) {
                $data['attr'] = isset($data['attr']) ? array_merge($this->default['attr'], $data['attr']) : $this->default['attr'];
                $data         = array_merge($this->default, $data);
                //处理$data['attr']['data']
                $data_str = '';
                if (isset($data['attr']['data']) && $data['attr']['data']) {
                    foreach ($data['attr']['data'] as $k => $v) {
                        $data_str .= 'data-' . $k . '="' . $v . '" ';
                    }
                }
                //is_pop
                if ($data['attr']['is_pop']) {
                    $data_str .= ' data-title="' . $data['title'] . '"';
                }
                if ($data['type'] == 'a') {
                    $html .= '<a ' . ($data['id'] ? 'id="' . $data['id'] . '"' : '') . ' href="' . $data['attr']['href'] . '" class="' . ($data['attr']['is_pop'] ? 'yf-modal-open' : '') . ' ' . $data['attr']['class'] . ' ' . ($data['attr']['disabled'] ? 'disabled' : '') . '" target="' . $data['attr']['target'] . '" ' . $data_str . ' ' . ($data['attr']['tips'] ? 'data-rel="tooltip" data-placement="bottom" data-original-title="' . $data['attr']['tips'] . '" ' : '') . '>';
                } else {
                    $html .= '<button ' . ($data['id'] ? 'id="' . $data['id'] . '"' : '') . ' class="' . ($data['attr']['is_pop'] ? 'yf-modal-open' : '') . ' ' . $data['attr']['class'] . ' ' . ($data['attr']['disabled'] ? 'disabled' : '') . '" type="' . $data['attr']['type'] . '" ' . $data_str . ' ' . ($data['attr']['tips'] ? 'data-rel="tooltip" data-placement="bottom" data-original-title="' . $data['attr']['tips'] . '" ' : '') . ' data-url="' . $data['attr']['href'] . '">';
                }
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
                $html .= $data['type'] == 'a' ? '</a>' : '</button>';
            }
            $html .= '</div>';
        }
        return $html;
    }
}
