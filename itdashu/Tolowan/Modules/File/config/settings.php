<?php
use Core\Config;

$settings = array(
    'formId' => 'adminFileSettingsForm',
    'form' => array(
        'action' => staticUrl(),
        'method' => 'get',
        'ajax-submit' => '#main',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminFileSettingsForm',
    ),
    'rename' => array(
        'label' => '文件重名',
        'userOptions' => array(),
        'description' => '是否重命名上传文件',
        'field' => 'boole',
        'widget' => 'Select',
        'value' => 1,
        'error' => '',
        'options' => array(
            1 => '开启',
            0 => '关闭',
        ),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'img_we' => array(
        'Group' => 'Group',
        'label' => '水印',
        'userOptions' => array(),
        'error' => '',
        'description' => '图片水印相关设置',
        'field' => 'group',
        'widget' => 'Group',
        'group' => array(
            'watermark' => array(
                'label' => '图片水印',
                'userOptions' => array(),
                'description' => '是否自动为上传图片添加水印',
                'field' => 'number',
                'error' => '',
                'widget' => 'Select',
                'value' => 0,
                'options' => array(
                    0 => '关闭',
                    1 => '文字水印',
                    2 => '图片水印',
                ),
                'validate' => array(),
                'attributes' => array(
                    'class' => 'form-control',
                ),
            ),
            'watermark_font_size' => array(
                'label' => '文字水印大小',
                'userOptions' => array(),
                'description' => '文字水印大小，单位px',
                'field' => 'number',
                'error' => '',
                'widget' => 'Text',
                'value' => 0,
                'validate' => array(),
                'attributes' => array(
                    'class' => 'form-control',
                ),
            ),
            'watermark_size' => array(
                'label' => '水印图片最小尺寸',
                'userOptions' => array(),
                'description' => '只为大于该大小的图片添加水印，单位KB',
                'field' => 'number',
                'error' => '',
                'widget' => 'Text',
                'value' => 0,
                'validate' => array(),
                'attributes' => array(
                    'class' => 'form-control',
                ),
            ),
            'watermark_opacity' => array(
                'label' => '水印透明度',
                'userOptions' => array(),
                'description' => '水印显示透明度',
                'field' => 'number',
                'error' => '',
                'widget' => 'Text',
                'value' => 0,
                'validate' => array(),
                'attributes' => array(
                    'class' => 'form-control',
                ),
            ),
            'watermark_text' => array(
                'label' => '文本水印',
                'userOptions' => array(),
                'description' => '文本水印内容',
                'field' => 'string',
                'error' => '',
                'widget' => 'Text',
                'value' => 0,
                'validate' => array(),
                'attributes' => array(
                    'class' => 'form-control',
                ),
            ),
            'watermark_image' => array(
                'label' => '图片水印',
                'userOptions' => array(),
                'description' => '图片水印内容',
                'field' => 'file',
                'error' => '',
                'widget' => 'File',
                'value' => 0,
                'watermark' => 0,
                'validate' => array(),
                'attributes' => array(
                    'class' => 'form-control',
                ),
            ),
        ),
        'validate' => array(),
        'attributes' => array(),
        'required' => false,
    ),
    'roles' => array(
        'label' => '角色相关设置',
        'userOptions' => array(),
        'error' => '',
        'description' => '为角色设置相关上传、操作权限',
        'field' => 'groupTabs',
        'widget' => 'GroupTabs',
        'groupTabs' => array(),
        'validate' => array(),
        'attributes' => array(),
        'required' => false,
    ),
    'settings' => array(
        'save' => 'config',
        'data' => 'm.file.config',
        'menuGroup' => array(),
        'module' => '内容',
        'title' => '设置',
        'description' => '内容相关设置',
    ),
);
$rolesOptions = array();
$rolesList = Config::get('m.user.roles');
foreach ($rolesList as $key => $roles) {
    $rolesOptions[$key] = $roles['name'];
    $settings['roles']['groupTabs'][$key] = array(
        'Group' => 'Group',
        'label' => $roles['name'],
        'userOptions' => array(),
        'error' => '',
        'description' => $roles['name'] . '相关上传设置',
        'field' => 'group',
        'widget' => 'Group',
        'group' => array(
            $key . '_upload_access' => array(
                'label' => '上传权限',
                'userOptions' => array(),
                'error' => '',
                'description' => '允许上传文件权限',
                'field' => 'list',
                'widget' => 'Checkboxs',
                'options' => array(
                    1 => '上传私有文件',
                    2 => '上传共有文件',
                ),
                'validate' => array(),
                'attributes' => array(),
                'required' => false,
            ),
            $key . '_upload_type' => array(
                'label' => '上传类型',
                'userOptions' => array(),
                'error' => '',
                'description' => '允许上传文件类型',
                'field' => 'list',
                'widget' => 'Selects',
                'options' => Config::get('contentType'),
                'validate' => array(),
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'required' => false,
            ),
            $key . '_upload_size_min' => array(
                'label' => '最小文件',
                'userOptions' => array(),
                'error' => '',
                'description' => '允许上传文件的大小限制，单位：kb',
                'field' => 'number',
                'widget' => 'Text',
                'validate' => array(),
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'required' => true,
            ),
            $key . '_upload_size_max' => array(
                'label' => '最大文件',
                'userOptions' => array(),
                'error' => '',
                'description' => '允许上传文件的最大限制，单位：kb',
                'field' => 'number',
                'widget' => 'Text',
                'validate' => array(),
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'required' => true,
            ),
        ),
        'validate' => array(),
        'attributes' => array(),
        'required' => false,
    );
}
$settings['private_img_down']['settings']['options'] = $rolesOptions;
unset($key);
unset($roles);
unset($rolesOptions);
