<?php
use Phalcon\DI;

$settings = array(
    'formId' => 'adminNodeFilterForm',
    'formName' => '过滤',
    'layout' => 'inline',
    'form' => array(
        'action' => staticUrl(),
        'method' => 'get',
        'class' => 'form-inline',
        'ajax-submit' => '#main',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminNodeFilterForm',
    ),
    'contentModel' => array(
        'label' => '文章类型',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'string',
        'widget' => 'Select',
        'options' => array(
            'null' => '不限制'
        ),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => false,
    ),
    'state' => array(
        'label' => '状态',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'string',
        'widget' => 'Select',
        'options' => array(
            'null' => '不限制',
            1 => '正常',
            2 => '回收站',
            3 => '待审核',
        ),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => false,
    ),
    'top' => array(
        'label' => '置顶',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'string',
        'widget' => 'Select',
        'options' => array(
            'null' => '不限制',
            '非置顶',
            '置顶',
        ),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => false,
    ),
    'essence' => array(
        'label' => '精华',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'string',
        'widget' => 'Select',
        'options' => array(
            'null' => '不限制',
            '非精华',
            '精华',
        ),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => false,
    ),
    'hot' => array(
        'label' => '热点',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'string',
        'widget' => 'Select',
        'options' => array(
            'null' => '不限制',
            '非热点',
            '热点',
        ),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => false,
    ),
    'uid' => array(
        'label' => '用户',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'number',
        'widget' => 'Text',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => false,
    ),
    'settings' => array(
        'checkToken' => false,
        'validation' => true,
    ),
);
foreach (Di::getDefault()->getEntityManager()->get('node')->getContentModelList() as $key => $value) {
    $settings['contentModel']['options'][$key] = $value['modelName'];
}
unset($key);
unset($value);
