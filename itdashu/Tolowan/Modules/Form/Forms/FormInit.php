<?php
namespace Modules\Form\Forms;

use Core\Config;

class FormInit
{
    public static $element = array();
    public static $validate = array();
    public static $field = array();

    public static function init()
    {
        if (!self::$element) {
            self::initElement();
        }
        if (!self::$validate) {
            self::initValidate();
        }
        if (!self::$field) {
            self::initField();
        }
    }

    public static function initElement()
    {
        $default = array(
            'Checkboxs' => '\Modules\Form\Forms\FormElementInit::checkboxs',
            'Group' => '\Modules\Form\Forms\FormElementInit::group',
            'GroupTabs' => '\Modules\Form\Forms\FormElementInit::groupTabs',
            'MachineName' => '\Modules\Form\Forms\FormElementInit::machineName',
            'Radio' => '\Modules\Form\Forms\FormElementInit::radio',
            'Autoinput' => '\Modules\Form\Forms\FormElementInit::Autoinput',
            'Chosen' => '\Modules\Form\Forms\FormElementInit::Chosen',
            'Radios' => '\Modules\Form\Forms\FormElementInit::radios',
            'Select' => '\Modules\Form\Forms\FormElementInit::select',
            'Selects' => '\Modules\Form\Forms\FormElementInit::selects',
            'Password' => '\Modules\Form\Forms\FormElementInit::password',
            'Email' => '\Modules\Form\Forms\FormElementInit::email',
            'Checkbox' => '\Modules\Form\Forms\FormElementInit::check',
            'Text' => '\Modules\Form\Forms\FormElementInit::text',
            'ValidateCode' => '\Modules\Form\Forms\FormElementInit::validateCode',
            'Tags' => '\Modules\Form\Forms\FormElementInit::Tags',
            'Date' => '\Modules\Form\Forms\FormElementInit::date',
            'Textarea' => '\Modules\Form\Forms\FormElementInit::textarea',
            'Submit' => '\Modules\Form\Forms\FormElementInit::submit',
            'Numeric' => '\Modules\Form\Forms\FormElementInit::numeric',
            'Hidden' => '\Modules\Form\Forms\FormElementInit::hidden',
            'Kvgroup' => '\Modules\Form\Forms\FormElementInit::kvgroup',
        );
        $config = Config::cache('formElement');
        self::$element = array_merge($default, $config);
        return self::$element;

    }

    public static function initValidate()
    {
        $default = array(
            'PresenceOf' => '\Modules\Form\Forms\FormValidationInit::presenceOf',
            'Identical' => '\Modules\Form\Forms\FormValidationInit::identical',
            'Function' => '\Modules\Form\Forms\FormValidationInit::functions',
            'Email' => '\Modules\Form\Forms\FormValidationInit::email',
            'ExclusionIn' => '\Modules\Form\Forms\FormValidationInit::exclusionIn',
            'InclusionIn' => '\Modules\Form\Forms\FormValidationInit::inclusionIn',
            'Regex' => '\Modules\Form\Forms\FormValidationInit::regex',
            'StringLength' => '\Modules\Form\Forms\FormValidationInit::stringLength',
            'Between' => '\Modules\Form\Forms\FormValidationInit::between',
            'Confirmation' => '\Modules\Form\Forms\FormValidationInit::confirmation',
        );
        $config = Config::cache('formValidate');
        self::$validate = array_merge($default, $config);
        return self::$validate;
    }

    public static function initField()
    {
        $default = array(
            'unknown' => array(
                'name' => '自由',
            ),
            'number' => array(
                'name' => '数字',
                'widget' => array(
                    'Text' => '文本框',
                    'Checkbox' => '复选框',
                    'Select' => '下拉列表',
                    'Hidden' => '隐藏表单',
                    'Radios' => '单选按钮组',
                    'Autoinput' => '自动完成组',
                    'Chosen' => '自动完成的下拉列表'
                ),
                'settings' => array(),
                'validate' => array(
                    array(
                        'v' => 'Regex',
                        'pattern' => '|[0-9]{0,}|',
                        'message' => '必须是整数',
                    ),
                ),
                'filter' => array('int'),
            ),
            'email' => array(
                'name' => '邮箱',
                'widget' => array(
                    'Email' => '文本框',
                ),
                'settings' => array(),
                'validate' => array(
                    array(
                        'v' => 'Email',
                    ),
                ),
                'filter' => array('email'),
            ),
            'date' => array(
                'name' => '时间',
                'widget' => array(
                    'Date' => '日期框',
                ),
                'settings' => array(),
                'validate' => array(),
                'filter' => array(),
            ),
            'boole' => array(
                'name' => '布尔',
                'widget' => array(
                    'Radios' => '单选按钮组',
                    'Text' => '文本框',
                    'Checkbox' => '复选框',
                    'Select' => '下拉列表',
                    'Autoinput' => '自动完成组'
                ),
                'validate' => array(
                    array(
                        'v' => 'Regex',
                        'pattern' => '|[0-1]{0,}|',
                        'message' => '必须是1或者0',
                    ),
                ),
                'settings' => array(),
                'filter' => array(),
            ),
            'list' => array(
                'name' => '多选列表',
                'widget' => array(
                    'Selects' => '多选列表',
                    'Checkboxs' => '复选框组',
                ),
                'settings' => array(),
                'validate' => array(),
                'filter' => array(),
            ),
            'image' => array(
                'name' => '图像',
                'widget' => array(
                    'file' => '文件控件',
                ),
                'settings' => array(),
                'validate' => array(),
                'filter' => array(),
            ),
            'validateCode' => array(
                'name' => '图像',
                'widget' => array(
                    'ValidateCode' => '验证码',
                ),
                'settings' => array(),
                'validate' => array(),
                'filter' => array(),
            ),
            'string' => array(
                'name' => '字符串',
                'widget' => array(
                    'Text' => '单行文本框',
                    'Password' => '密码框',
                    'Checkbox' => '复选框',
                    'Checkboxs' => '多选复选框',
                    'Select' => '下拉列表',
                    'Selects' => '多选下拉列表',
                    'Radios' => '单选按钮组',
                    'Hidden' => '隐藏文本框',
                    'Autoinput' => '自动完成组',
                    'Chosen' => '自动完成的下拉列表'
                ),
                'settings' => array(),
                'validate' => array(),
                'filter' => array(),
            ),
            'file' => array(
                'name' => '文件',
                'widget' => array(
                    'File' => '文件',
                ),
                'settings' => array(),
                'validate' => array(),
                'filter' => array(),
            ),
            'group' => array(
                'name' => '标签组',
                'widget' => array(
                    'Group' => '标签组',
                ),
                'settings' => array(),
                'validate' => array(),
                'filter' => array(),
            ),
            'kvgroup' => array(
                'name' => '字段组',
                'widget' => array(
                    'Kvgroup' => '字段组',
                ),
                'settings' => array(),
                'validate' => array(),
                'filter' => array(),
            ),
            'groupTabs' => array(
                'name' => '字段组切换标签',
                'widget' => array(
                    'GroupTabs' => '字段组手风琴',
                ),
                'settings' => array(),
                'validate' => array(),
                'filter' => array(),
            ),
            'textLong' => array(
                'name' => '长文本',
                'widget' => array(
                    'Textarea' => '多行文本框',
                ),
                'settings' => array(),
                'validate' => array(),
                'filter' => array(),
            ),
            'summary' => array(
                'name' => '摘要文本',
                'widget' => array(
                    'Textarea' => '多行文本框',
                    'Text' => '文本框',
                ),
                'validate' => array(
                    array(
                        'v' => 'StringLength',
                        'max' => 255,
                        'min' => 0,
                    ),
                ),
                'settings' => array(),
                'filter' => array(),
            ),
        );

        $field = array_merge($default, Config::cache('field'));
        self::$field = $field;
    }

    public static function getFieldInfo(){
        self::initField();
        $data = array();
        foreach (self::$field as $key => $field){
            $item = array('s' => array());
            $item = array('name'=>$field['name'],'value'=>$key);
            foreach($field['widget'] as $wk => $wv){
                $item['s'][] = array('name'=>$wv,'value'=>$wk);
            }
            $data[] = $item;
        }
        return $data;
    }
    public static function callElement(&$t, &$element)
    {
        if (isset(self::$element[$element['widget']])) {
            return self::$element[$element['widget']]($t,$element);
        } else {
            return false;
        }
    }

    public static function callValidate(&$t, $validate)
    {
        if (isset($validate['v']) && isset(self::$validate[$validate['v']])) {
            return self::$validate[$validate['v']]($t,$validate);
        } else {
            return false;
        }
    }

    public static function callField(&$name, &$element)
    {
        $element = array_merge(array(
            'validate' => array(),
            'filter' => array(),
            'settings' => array(),
        ), $element);
        $field = $element['field'];
        if (!isset(self::$field[$field]) || !isset(self::$field[$field]['widget'][$element['widget']])) {
            return false;
        }
        self::$field[$field] += array(
            'validate' => array(),
            'filter' => array(),
            'settings' => array(),
        );
        $element['validate'] = array_merge(self::$field[$field]['validate'], $element['validate']);
        $element['filter'] = array_merge(self::$field[$field]['filter'], $element['filter']);
        $element['settings'] = array_merge(self::$field[$field]['settings'], $element['settings']);
        if (isset(self::$field[$field]['init']) && is_callable(self::$field[$field]['init'])) {
            return call_user_func_array(self::$field[$field]['init'], array(&$name, &$element));
        }
    }
}
