<?php
use Core\Config;

$nodeType = Config::get('m.node.type');
$nodeTypeOptions = array();
foreach ($nodeType as $key => $value) {
    $nodeTypeOptions[$key] = $value['name'];
}
$settings = array(
    'formId' => 'adminCommentSettingsForm',
    'form' => array(
        'action' => staticUrl(),
        'method' => 'get',
        'ajax-submit' => '#main',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminCommentSettingsForm',
    ),
    'open_comment' => array(
        'label' => '开启评论',
        'userOptions' => array(),
        'error' => '',
        'description' => '是否开启评论',
        'field' => 'boole',
        'widget' => 'Radios',
        'options' => array(
            0 => '关闭',
            1 => '开启',
        ),
        'value' => 1,
        'validate' => array(),
        'attributes' => array(),
        'required' => true,
    ),
    'number' => array(
        'label' => '评论默认显示数量',
        'userOptions' => array(),
        'error' => '',
        'description' => '评论列表初始化显示评论的数量',
        'field' => 'number',
        'widget' => 'Text',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'interval' => array(
        'label' => '评论间隔',
        'userOptions' => array(),
        'error' => '',
        'description' => '填入整数，时间单位为秒，用户多次评论最少的时间间隔',
        'field' => 'number',
        'widget' => 'Text',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'sort' => array(
        'label' => '首选评论排序',
        'userOptions' => array(),
        'error' => '',
        'description' => '在内容页和评论页评论的默认排序',
        'field' => 'string',
        'widget' => 'Radios',
        'options' => array(
            'timeDown' => '时间<降序>',
            'timeUp' => '时间<升序>',
            'hotDown' => '热度<降序>',
            'hotUp' => '热度<升序>',
        ),
        'validate' => array(),
        'attributes' => array(),
        'required' => true,
    ),
    'comment_type' => array(
        'label' => '内容评论',
        'userOptions' => array(),
        'error' => '',
        'description' => '为以下内容类型开启评论',
        'field' => 'list',
        'widget' => 'Checkboxs',
        'options' => $nodeTypeOptions,
        'validate' => array(),
        'attributes' => array(),
        'required' => true,
    ),
    'roles' => array(
        'label' => '角色设置',
        'userOptions' => array(),
        'error' => '',
        'description' => '评论模块角色相关设置',
        'field' => 'groupTabs',
        'widget' => 'GroupTabs',
        'validate' => array(),
        'attributes' => array(),
        'groupTabs' => array(),
        'required' => false,
    ),
    'settings' => array(
        'save' => 'config',
        'menuGroup' => array(),
        'data' => 'm.comment.config',
        'module' => '评论',
        'title' => '设置',
        'description' => '评论模块设置',
    ),
);
unset($nodeType);
unset($nodeTypeOptions);
$rolesList = Config::get('m.user.roles');
foreach ($rolesList as $key => $value) {
    $settings['roles']['groupTabs'][$key] = array(
        'label' => $value['name'],
        'userOptions' => array(),
        'error' => '',
        'description' => $value['name'] . '评论设置',
        'field' => 'group',
        'widget' => 'Group',
        'group' => array(
            $key . '_open' => array(
                'label' => '开启评论',
                'userOptions' => array(),
                'error' => '',
                'description' => '是否为此角色开启评论',
                'field' => 'boole',
                'widget' => 'Radios',
                'options' => array(
                    0 => '关闭',
                    1 => '开启'
                ),
                'validate' => array(),
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'required' => true,
            ),
            $key . '_filter' => array(
                'label' => '内容输入',
                'userOptions' => array(),
                'error' => '',
                'description' => '输入允许的评论中允许包含的html标签',
                'field' => 'textLong',
                'widget' => 'Textarea',
                'validate' => array(),
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'required' => false,
            ),
            $key . '_display' => array(
                'label' => '显示评论',
                'userOptions' => array(),
                'error' => '',
                'description' => '输入允许的评论中允许包含的html标签',
                'field' => 'textLong',
                'widget' => 'Textarea',
                'validate' => array(),
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'required' => false,
            ),
        ),
        'validate' => array(),
        'attributes' => array(),
        'required' => false,
    );
}