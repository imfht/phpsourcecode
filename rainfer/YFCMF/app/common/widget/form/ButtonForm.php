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
 * 按钮
 * @Author: rainfer <rainfer520@qq.com>
 */
class ButtonForm
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
            //附加属性
            'data'     => []
        ]
    ];

    /**
     * 渲染
     *
     * @param string $title 标题
     * @param array  $attr  属性，
     * @param string $id
     * @param string $type  ''或'a'
     *
     * @return string
     */
    public function fetch($title = '', $attr = [], $id = '', $type = '')
    {
        $data         = [
            'id'    => $id,
            'title' => $title,
            'type'  => $type,
            'attr'  => $attr
        ];
        $data['attr'] = isset($data['attr']) ? array_merge($this->default['attr'], $data['attr']) : $this->default['attr'];
        $data         = array_merge($this->default, $data);
        //处理$data['attr']['data']
        $data_str = '';
        if (isset($data['attr']['data']) && $data['attr']['data']) {
            foreach ($data['attr']['data'] as $k => $v) {
                $data_str .= 'data-' . $k . '="' . $v . '" ';
            }
        }
        if ($data['type'] == 'a') {
            $html = '<a ' . ($data['id'] ? 'id="' . $data['id'] . '"' : '') . ' href="' . $data['attr']['href'] . '" class="' . $data['attr']['class'] . ' ' . ($data['attr']['disabled'] ? 'disabled' : '') . '" target="' . $data['attr']['target'] . '" ' . $data_str . ' ' . ($data['attr']['tips'] ? 'data-rel="tooltip" data-placement="bottom" data-original-title="' . $data['attr']['tips'] . '" ' : '') . '>';
        } else {
            $html = '<button ' . ($data['id'] ? 'id="' . $data['id'] . '"' : '') . ' class="' . $data['attr']['class'] . ' ' . ($data['attr']['disabled'] ? 'disabled' : '') . '" type="' . $data['attr']['type'] . '" ' . $data_str . ' ' . ($data['attr']['tips'] ? 'data-rel="tooltip" data-placement="bottom" data-original-title="' . $data['attr']['tips'] . '" ' : '') . '>';
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
        return $html;
    }
}
