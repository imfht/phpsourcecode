<?php
namespace Modules\Form\Forms;

class FormValidationInit
{
    public static function presenceOf(&$t, $validate)
    {
        if (!isset($validate['message']) || empty($validate['message'])) {
            $validate['message'] = '值不能为空';
        }
        $t->setAttribute('required','true');
        $t->addValidator(new \Phalcon\Validation\Validator\PresenceOf($validate));
    }

    public static function identical(&$t, $validate)
    {
        if (!isset($validate['message']) || empty($validate['message'])) {
            $validate['message'] = '您必须要接受这个选项才能继续。';
        }
        $t->addValidator(new \Phalcon\Validation\Validator\Identical($validate));
    }

    public static function functions(&$t, $validate)
    {
        if (!isset($validate['message']) || empty($validate['message'])) {
            $validate['message'] = '验证失败。';
        }
        $t->addValidator(new \Core\Validation\FunctionValidator($validate));
    }

    public static function email(&$t, $validate)
    {
        if (!isset($validate['message']) || empty($validate['message'])) {
            $validate['message'] = '邮件地址不合法';
        }
        $t->addValidator(new \Phalcon\Validation\Validator\Email($validate));
    }

    public static function exclusionIn(&$t, $validate)
    {
        if (!isset($validate['message']) || empty($validate['message'])) {
            $validate['message'] = '值不能包含' . implode('，', $validate['domain']);
        }
        $t->addValidator(new \Phalcon\Validation\Validator\ExclusionIn($validate));
    }

    public static function inclusionIn(&$t, $validate)
    {
        if (!isset($validate['message']) || empty($validate['message'])) {
            $validate['message'] = '值必须是以下值其中之一：' . implode('，', $validate['domain']);
        }
        $t->addValidator(new \Phalcon\Validation\Validator\InclusionIn($validate));
    }

    public static function regex(&$t, $validate)
    {
        if (!isset($validate['message']) || empty($validate['message'])) {
            $validate['message'] = '字段不合法';
        }
        $t->setAttribute('regex',$validate['pattern']);
        $t->addValidator(new \Phalcon\Validation\Validator\Regex($validate));
    }

    public static function stringLength(&$t, $validate)
    {
        if (!isset($validate['messageMaximum']) || empty($validate['messageMaximum'])) {
            $validate['messageMaximum'] = '值的长度不能大于' . $validate['max'];
        }
        if (!isset($validate['messageMinimum']) || empty($validate['messageMinimum'])) {
            $validate['messageMinimum'] = '值的长度不能小于' . $validate['min'];
        }
        if(isset($validate['max']) && isset($validate['min'])){
            $t->setAttribute('rangelength','['.$validate['min'].','.$validate['max'].']');
        }elseif(isset($validate['max'])){
            $t->setAttribute('maxlength',$validate['max']);
        }elseif(isset($validate['min'])){
            $t->setAttribute('minlength',$validate['min']);
        }
        $t->addValidator(new \Phalcon\Validation\Validator\StringLength($validate));
    }

    public static function between(&$t, $validate)
    {
        if (!isset($validate['message']) || empty($validate['message'])) {
            $validate['message'] = '数值必须在　' . $validate['minimum'] . '　和　' . $validate['maximum'] . '　之间';
        }
        $t->setAttribute('rangelength','['.$validate['minimum'].','.$validate['maximum'].']');
        $t->addValidator(new \Phalcon\Validation\Validator\Between($validate));
    }

    public static function confirmation(&$t, $validate)
    {
        if (!isset($validate['message']) || empty($validate['message'])) {
            $validate['message'] = '值不合法';
        }
        $t->addValidator(new \Phalcon\Validation\Validator\Confirmation($validate));
    }
}