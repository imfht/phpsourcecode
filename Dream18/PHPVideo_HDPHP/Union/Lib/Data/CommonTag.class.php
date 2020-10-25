<?php
class CommonTag
{
    /**
     * 标签声明
     * @var array
     */
    public $Tag = array(
        'test' => array('block' => 1, 'level' => 4),
    );

    /**
     * 测试标签
     * @param $attr 属性
     * @param $content 内容
     * @param $hd HdView模型引擎对象
     */
    public function _test($attr, $content, &$hd)
    {

    }
}