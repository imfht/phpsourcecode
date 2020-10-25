<?php
$settings = array(
    'formId' => 'adminInstallTest',
    'form' => array(
        'action' => staticUrl(),
        'method' => 'get',
        'ajax-submit' => '#main',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminInstallTest',
    ),
    'password' => array(
        'label' => '安装密码',
        'userOptions' => array(),
        'error' => '',
        'description' => '输入安装密码才能在该服务器安装新站点',
        'field' => 'string',
        'widget' => 'Password',
        'value' => 1,
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'settings' => array(
        'save' => '\Modules\Install\Library\Common::validationSave',
    ),
);