<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------

/**
 * Builder配置文件
 */
return array(
    //表单类型
    'FORM_ITEM_TYPE' => array(
        'hidden'     => array('隐藏', 'varchar(32) NOT NULL'),
        'num'        => array('数字', 'int(11) UNSIGNED NOT NULL'),
        'text'       => array('字符串', 'varchar(128) NOT NULL'),
        'textarea'   => array('文本', 'varchar(256) NOT NULL'),
        'array'      => array('数组', 'varchar(32) NOT NULL'),
        'password'   => array('密码', 'varchar(64) NOT NULL'),
        'radio'      => array('单选按钮', 'varchar(32) NOT NULL'),
        'checkbox'   => array('复选框', 'varchar(32) NOT NULL'),
        'select'     => array('下拉框', 'varchar(32) NOT NULL'),
        'icon'       => array('图标', 'varchar(32) NOT NULL'),
        'date'       => array('日期', 'int(11) UNSIGNED NOT NULL'),
        'time'       => array('时间', 'int(11) UNSIGNED NOT NULL'),
        'picture'    => array('图片', 'int(11) UNSIGNED NOT NULL'),
        'pictures'   => array('图片(多图)', 'varchar(32) NOT NULL'),
        'file'       => array('文件', 'varchar(32) NOT NULL'),
        'files'      => array('多文件', 'varchar(32) NOT NULL'),
        'kindeditor' => array('编辑器 kindeditor', 'text'),
        'simditor'   => array('编辑器 simditor', 'text'),
        'tags'       => array('标签', 'varchar(128) NOT NULL'),
        'board  '    => array('拖动排序', 'varchar(256) NOT NULL')
    )
);
