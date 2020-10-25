<?php
namespace Modules\Core\Library;

use Core\Config;

class OptionsList
{
    public static function save($form)
    {
        $data = $form->getData();
        $formEntity = $form->formEntity;
        $settings = $formEntity['settings'];
        $optionsList = Config::get($settings['dataId']);
        if (isset($settings['id'])) {
            $data['machine'] = $id = $settings['id'];
        } else {
            $id = $data['machine'];
        }
        $optionsList[$id] = $data;
        if (Config::set($settings['dataId'], $optionsList)) {
            return true;
        } else {
            return false;
        }
    }

    public static function put($name, $key, $data)
    {
        $data = Config::get($name);
        $data[$key] = $data;
        if (Config::set($name, $data)) {
            return true;
        } else {
            return false;
        }
    }
    public static function remove($name, $key)
    {
        $data = Config::get($name);
        unset($data[$key]);
        if (Config::set($name, $data)) {
            return true;
        } else {
            return false;
        }
    }
}
