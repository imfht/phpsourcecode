<?php
namespace Modules\Form\Forms;

class FormElementInit
{

    public static function text(&$t, &$element)
    {
        $field = new \Phalcon\Forms\Element\Text($element['name'], $element['attributes']);
        return $field;
    }

    public static function hidden(&$t, &$element)
    {
        $field = new \Phalcon\Forms\Element\Hidden($element['name'], $element['attributes']);
        return $field;
    }

    public static function Tags(&$t, &$element)
    {
        $field = new \Modules\Form\Forms\Element\Tags($element['name'], $element['attributes']);
        return $field;
    }

    public static function Autoinput(&$t, &$element)
    {
        $field = new \Modules\Form\Forms\Element\Autoinput($element['name'], $element['attributes']);
        return $field;
    }

    public static function Chosen(&$t, &$element)
    {
        if (!isset($element['options'])) {
            $element['options'] = array();
        }
        $field = new \Modules\Form\Forms\Element\Chosen($element['name'], $element['options'], $element['attributes']);
        return $field;
    }

    public static function checkboxs(&$t, &$element)
    {
        $field = new \Modules\Form\Forms\Element\Checkboxes($element['name'], $element['attributes']);
        $field->setOptions($element['options']);
        return $field;
    }

    public static function group(&$t, &$element)
    {
        $field = new \Modules\Form\Forms\Element\Group($element['name'], $element['attributes']);
        $field->setGroup($element['group']);
        $t->addField($element['group']);
        return $field;
    }

    public static function groupTabs(&$t, &$element)
    {
        $field = new \Modules\Form\Forms\Element\GroupTabs($element['name'], $element['attributes']);
        $field->setGroupTabs($element['groupTabs']);
        $t->addField($element['groupTabs']);
        return $field;
    }

    public static function validateCode(&$t, &$element)
    {
        $field = new \Modules\Form\Forms\Element\ValidateCode($element['name'], $element['attributes']);
        return $field;
    }

    public static function machineName(&$t, &$element)
    {
        $field = new \Modules\Form\Forms\Element\MachineName($element['name'], $element['attributes']);
        return $field;
    }

    public static function radios(&$t, &$element)
    {
        $field = new \Modules\Form\Forms\Element\Radios($element['name'], $element['attributes']);
        $field->setOptions($element['options']);
        return $field;
    }

    public static function kvgroup(&$t, &$element)
    {
        $field = new \Modules\Form\Forms\Element\Kvgroup($element['name'], $element['attributes']);
        return $field;
    }

    public static function radio(&$t, &$element)
    {
        $field = new \Phalcon\Forms\Element\Radio($element['name'], $element['attributes']);
        return $field;
    }

    public static function select(&$t, &$element)
    {
        $field = new \Phalcon\Forms\Element\Select($element['name'], $element['options'], $element['attributes']);
        if (!empty($element['options'])) {
            $element['validate'][] = array(
                'v' => 'InclusionIn',
                'message' => '您的输入内容超出了范围',
                'domain' => array_keys($element['options']),
            );
        }
        return $field;
    }

    public static function selects(&$t, &$element)
    {
        $field = new \Modules\Form\Forms\Element\Selects($element['name'], $element['options'], $element['attributes']);
        return $field;
    }

    public static function check(&$t, &$element)
    {
        $field = new \Phalcon\Forms\Element\Check($element['name'], $element['attributes']);
        if (!isset($element['value'])) {
            $element['value'] = 1;
        }
        $field->setDefault($element['value']);
        return $field;
    }

    public static function date(&$t, &$element)
    {
        global $di;
        $name = $element['name'];
        $field = new \Phalcon\Forms\Element\Date($element['name'], $element['attributes']);
        if (!isset($element['fmt'])) {
            $element['fmt'] = 'yyyy-MM-dd hh:mm:ss';
        }
        $data = $t->getData();
        if (!isset($data[$name]) || !$data[$name]) {
            $data[$name] = time();
        }
        $date = date('Y-m-d H:i:s', intval($data[$name]));
        $scription = <<<scription
        $('#{$name}').datetimepicker({
    format: 'yyyy-mm-dd hh:ii:ss',
    autoclose: true,
    language: 'zh-CN',
    startDate: '{$date}'
});
$('#{$name}').val('{$date}');
scription;
        $di->getShared('assets')
            ->addJs('bootstrap-datetimepicker', 'http://cdn.itdashu.com/library/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js', 'footer')
            ->addJs('bootstrap-datetimepicker-zh', 'http://cdn.itdashu.com/library/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js', 'footer')
            ->addCss('bootstrap-datetimepicker', 'http://cdn.itdashu.com/library/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css', 'footer')
            ->addInlineJs('bootstrap-datetimepicker-init-' . $name, $scription, 'footer');
        return $field;
    }

    public static function password(&$t, &$element)
    {
        $field = new \Phalcon\Forms\Element\Password($element['name'], $element['attributes']);
        return $field;
    }

    public static function email(&$t, &$element)
    {
        $field = new \Phalcon\Forms\Element\Email($element['name'], $element['attributes']);
        $element['validate'][] = array(
            'v' => 'Email',
            'message' => '请输入合法的email地址',
        );
        return $field;
    }

    public static function textarea(&$t, &$element)
    {
        $field = new \Modules\Form\Forms\Element\TextArea($element['name'], $element['attributes']);
        return $field;
    }

    public static function file(&$t, &$element)
    {
        $field = new \Phalcon\Forms\Element\File($element['name'], $element['attributes']);
        return $field;
    }
}
