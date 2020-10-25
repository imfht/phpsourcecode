<?php
// Buider公有控制器
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
// | 原作者：心云间、凝听
// +----------------------------------------------------------------------
namespace tpvue\admin\builder;


use tpvue\admin\controller\BaseController;

/**
 * AdminBuilder：快速建立管理页面。
 *
 * Class AdminBuilder
 * @package Admin\Builder
 */
abstract class AdminBuilder extends BaseController
{
    public function fetch($templateFile='',$vars =array(), $replace ='', $config = '') {
        //获取模版的名称
        //$template ='Builder/'.$templateFile;
        //显示页面
        //halt();
        //return $this->fetch('/builder/' . $templateFile);
        // echo parent::fetch('./application/admin/view/builder/'.$templateFile.'.html');
        return $this->view->fetch('/builder/' . $templateFile);
    }

    protected function compileHtmlAttr($attr) {
        if (!is_array($attr)) {
            return '';
        }
        $result = array();

        foreach($attr as $key=>$value) {
            $value = htmlspecialchars($value);
            $result[] = "$key=\"$value\"";
        }
        $result = implode(' ', $result);
        return $result;
    }
}

