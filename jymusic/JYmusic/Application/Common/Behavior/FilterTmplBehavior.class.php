<?php
// +-------------------------------------------------------------+
// | Copyright (c) 2014-2015 JYmusic音乐管理系统                 |
// +-------------------------------------------------------------
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Common\Behavior;
use Think\Behavior;

defined('THINK_PATH') or exit();

class FilterTmplBehavior extends Behavior {
    // 行为扩展的执行入口必须是run
    public function run(&$Content){
        if(C('TMPL_STRIP_SPACE')) {
            /* 去除html空格与换行 */
            $find           = array('~>\s+<~','~>(\s+\n|\r)~');
            $replace        = array('><','>');
            $Content    = preg_replace($find, $replace, $Content);
        }
        
    }
}