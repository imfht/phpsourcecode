<?php
namespace Modules\Form\Library;

use Core\Config;

class AdminForm
{
    public static function userFormEdit($form)
    {
        $data = $form->getData();
        $formEntity = $form->formEntity;
        $formList = Config::get('m.form.userFormList');
        if (isset($formEntity['settings']['id'])) {
            $data['id'] = $formEntity['settings']['id'];
            if (!isset($formList[$data['id']]['fields'])) {
                $formList[$data['id']]['fields'] = 'm.form.userFormFields' . ucfirst($data['id']);
            }
            $formList[$data['id']] = $data;
        } else {
            $data['fields'] = 'm.form.userFormFields' . ucfirst($data['id']);
            $formList[$data['id']] = $data;
        }
        if (isset($data['attach']) && !empty($data['attach'])) {
            $data = array_merge($data, self::attachFilter($data['attach']));
        }
        if (Config::set('m.form.userFormList', $formList)) {
            return $form;
        } else {
            return false;
        }
    }

    public static function userFormFieldEdit($form)
    {
        $data = $form->getData();
        $formEntity = $form->formEntity;
        $formList = Config::get('m.form.userFormList');
        $formId = $formEntity['settings']['form_id'];
        $fields = Config::get($formList[$formId]['fields']);
        if (isset($formEntity['settings']['id'])) {
            $data['id'] = $formEntity['settings']['id'];
        }
        if (isset($data['attach']) && !empty($data['attach'])) {
            $data = array_merge($data, self::attachFilter($data['attach']));
        }
        $fields[$data['id']] = $data;
        if (Config::set($formList[$formId]['fields'], $fields)) {
            return true;
        } else {
            return false;
        }
    }

    protected static function attachFilter($attach)
    {
        if (!is_array($attach)) {
            return array();
        }
        $data = array();
        foreach ($attach as $key => $item) {
            if (strpos($key, ':')) {
                $keyInfo = explode(trim($key, ' :'), ':');
                if (count($keyInfo) > 1) {
                    $data[$keyInfo[0]][$keyInfo[1]] = $item;
                }
            }
        }
        return $data;
    }
}