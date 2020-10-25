<?php
class CommonTag extends Tag
{
    /**
     * 标签声明
     * @var array
     */
    public $Tag = array(
        'Pintuer'   => array('block' => 0, 'level' => 0),
    );

    /**
     * Pintuer 前端框架标签
     * @param $attr 属性
     * @param $content 内容
     * @param $hd HdView模型引擎对象
     */
    public function _Pintuer($attr, $content, &$hd)
    {
        return "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n<script type=\"text/javascript\" src=\"__STATIC__/Pintuer/jquery-1.11.0.js\"></script>\n<link rel=\"stylesheet\" type=\"text/css\" href=\"__STATIC__/Pintuer/pintuer.css\" />\n<script type=\"text/javascript\" src=\"__STATIC__/Pintuer/pintuer.js\"></script>\n<script type=\"text/javascript\" src=\"__STATIC__/Pintuer/respond.js\"></script>";
    }
}