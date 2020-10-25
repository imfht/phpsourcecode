<?php
$settings = array(
    'formId' => 'adminNodeSettingsForm',
    'form' => array(
        'action' => staticUrl(),
        'method' => 'post',
        'ajax-submit' => '#main',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminNodeSettingsForm',
    ),
    'browse' => array(
        'label' => '浏览统计',
        'userOptions' => array(),
        'description' => '启用浏览统计',
        'field' => 'boole',
        'widget' => 'Select',
        'options' => array(
            '不启用','启用'
        ),
        'error' => '',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'number' => array(
        'label' => '内容类型列表数量',
        'userOptions' => array(),
        'description' => '默认内容类型页列表数量',
        'field' => 'number',
        'widget' => 'Text',
        'error' => '',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'open_pagination' => array(
        'label' => '启用内容分页',
        'userOptions' => array(),
        'description' => '为以下内容启用内容分页',
        'field' => 'string',
        'widget' => 'Selects',
        'default' => 'article',
        'error' => '',
        'options' => nodeTypeList(),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'pagination' => array(
        'label' => '内容分页',
        'userOptions' => array(),
        'description' => '输入0关闭分页，输入1开启分页但需要手动分页，输入大于1的数字，为所有内容启用默认分页，并按输入的数字进行内容分割。',
        'field' => 'number',
        'widget' => 'Text',
        'error' => '',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'pagination_tag' => array(
        'label' => '内容分页标签',
        'userOptions' => array(),
        'description' => '使用的手动内容分页符，例如：<!-- pagination -->',
        'field' => 'string',
        'widget' => 'Text',
        'default' => '<!-- pagination -->',
        'error' => '',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'term_number' => array(
        'label' => '术语内容页列表数量',
        'userOptions' => array(),
        'description' => '术语内容页列表数量',
        'field' => 'number',
        'widget' => 'Text',
        'error' => '',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'settings' => array(
        'save' => 'config',
        'data' => 'm.node.config',
        'menuGroup' => array(),
        'module' => '内容',
        'title' => '设置',
        'description' => '内容相关设置',
    ),
);
