<?php
$settings = array(
    'unknown' => array(
        'name' => '自由',
    ),
    'number' => array(
        'name' => '数字',
        'widget' => array(
            'Text' => '文本框',
            'Checkbox' => '复选框',
        ),
        'validate' => array(
            array(
                'v' => 'Regex',
                'pattern' => '|[0-9]+|',
                'message' => '必须是整数',
            )
        ),
        'filter' => array('int')
    ),
    'email' => array(
        'name' => '邮箱',
        'widget' => array(
            'Text' => '文本框',
        ),
        'validate' => array(
            array(
                'v' => 'Email'
            )
        ),
        'filter' => array('email')
    ),
    'list' => array(
        'name' => '列表',
        'widget' => array(
            'Radio' => '单选框',
            'Select' => '下拉列表',
        ),
        'validate' => array(),
        'filter' => array()
    ),
    'date' => array(
        'name' => '时间',
        'widget' => array(
            'Date' => '日期框',
        ),
        'validate' => array(),
        'filter' => array()
    ),
    'boole' => array(
        'name' => '布尔',
        'widget' => array(
            'Radios' => '单选按钮组',
            'Text' => '文本框',
            'Checkbox' => '复选框',
        ),
        'validate' => array(
            array(
                'v' => 'Regex',
                'pattern' => '|[0-1]{1}|',
                'message' => '必须是整数',
            )
        ),
        'filter' => array()
    ),
    'lists' => array(
        'name' => '多选列表',
        'widget' => array(
            'Selects' => '多选列表',
            'Checkboxs' => '多选复选框',
        ),
        'validate' => array(),
        'filter' => array()
    ),
    'image' => array(
        'name' => '图像',
        'widget' => array(
            'file' => '文件控件',
        ),
        'validate' => array(),
        'filter' => array()
    ),
    'string' => array(
        'name' => '字符串',
        'widget' => array(
            'Text' => '单行文本框',
            'Password' => '密码框',
            'Checkbox' => '复选框',
            'Hidden' => '隐藏文本框',
        ),
        'validate' => array(),
        'filter' => array()
    ),
    'file' => array(
        'name' => '文件',
        'widget' => array(
            'File' => '文件',
        ),
        'validate' => array(),
        'filter' => array()
    ),
    'group' => array(
        'name' => '字段组',
        'widget' => array(
            'Group' => '字段组',
        ),
        'validate' => array(),
        'filter' => array()
    ),
    'groupTabs' => array(
        'name' => '字段组切换标签',
        'widget' => array(
            'GroupTabs' => '字段组手风琴',
        ),
        'validate' => array(),
        'filter' => array()
    ),
    'textLong' => array(
        'name' => '长文本',
        'widget' => array(
            'Textarea' => '多行文本框',
        ),
        'validate' => array(),
        'filter' => array()
    ),
    'summary' => array(
        'name' => '摘要文本',
        'widget' => array(
            'Textarea' => '多行文本框',
            'Text' => '文本框'
        ),
        'validate' => array(
            array(
                'v' => 'StringLength',
                'max' => 255,
                'min' => 0
            )
        ),
        'filter' => array()
    ),
);
