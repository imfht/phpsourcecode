<?php
$settings = array(
    'formId' => 'coreTranslate',
    'form' => array(
        'method' => 'post',
        'class' => array(),
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'coreTranslate',
    ),
    'translate' => array(
        'label' => '多语言支持',
        'userOptions' => array(),
        'description' => '是否启用多语言支持',
        'field' => 'boole',
        'widget' => 'Radios',
        'options' => array(
            0 => '关闭',
            1 => '开启'
        ),
        'error' => '',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'translate_type' => array(
        'label' => '多语言形式',
        'userOptions' => array(),
        'description' => '使用多语言的方式',
        'field' => 'number',
        'widget' => 'Select',
        'options' => array(
            1 => '子域名 如：en.domain.com',
            2 => '子目录 如：domain.com/en',
            3 => '请求参数 如：domain.com?lang=en',
            4 => 'cookie 通过cookie设置分辨语言'
        ),
        'error' => '',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'translate_language' => array(
        'label' => '启用语言',
        'userOptions' => array(),
        'description' => '单个内容节点查询数据缓存时间,时间为秒',
        'field' => 'string',
        'error' => '',
        'widget' => 'Checkboxs',
        'options' => array(
            'zh' => '中文',
            'en' => '英文',
            'jp' => '日文',
        ),
        'validate' => array(),
        'attributes' => array(),
    ),
    'settings' => array(
        'save' => 'config',
        'configId' => 'translate',
        'menuGroup' => array(),
        'module' => '系统',
        'title' => '多语言',
        'description' => '多语言相关设置',
    ),
);