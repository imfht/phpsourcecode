<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

/**
 * 获取当前班级名称
 * @param  integer $bj_code 班级代码
 * @return $bj_name     班级名称
 * @author 水月居 <singliang@163.com>
 */
function get_bjname($bj_code = 0)
{
    static $class_list;

    /* 获取缓存数据 */
    if (empty($class_list)) {
        $class_list = S('sys_class_list');
    }

    /* 查找班级名称 */
    $key = "b{$bj_code}";
    if (isset($class_list[$key])) { //已缓存，直接使用
        $bj_name = $class_list[$key];
    } else { //调用接口获取用户信息
        $info = M('XsClass')->field('bj_name')->where(array('bj_code'=>$bj_code))->find();
        if ($info !== false && $info['bj_name']) {
            $bj_name = $info['bj_name'];
            $class_list[$key] = $bj_name;
            /* 缓存班级 */
            $count = count($class_list);
            $max = 100;// C('USER_MAX_CACHE')
            while ($count-- > $max) {
                array_shift($class_list);//删除除第一个键值的数组
            }
            S('sys_class_list', $class_list);
        } else {
            $bj_name = '';
        }
    }
    return $bj_name;
}
