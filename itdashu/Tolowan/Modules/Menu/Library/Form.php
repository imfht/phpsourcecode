<?php
namespace Modules\Menu\Library;

use Core\Config;

class Form
{
    public static function saveLink($form)
    {
        global $di;
        $formData = $form->getData();
        $formEntity = $form->formEntity;
        $menuId = $formEntity['settings']['menuId'];
        $menuData = Config::get('m.menu.menu' . ucfirst($menuId) . 'Data');
        $menuHierarchy = Config::get('m.menu.menu' . ucfirst($menuId) . 'Hierarchy');
        if (isset($formEntity['settings']['id'])) {
            $id = $formEntity['settings']['id'];
            $menuData[$id] = array(
                'name' => $formData['name'],
                'description' => $formData['description'],
                'href' => self::toLink($formData['href']),
                'attach' => $formData['attach']
            );
        } else {
            $menuData[$formData['id']] = array(
                'name' => $formData['name'],
                'description' => $formData['description'],
                'href' => self::toLink($formData['href']),
                'attach' => $formData['attach']
            );
            $menuHierarchy[$formData['id']] = $formData['id'];
        }
        if (Config::set('m.menu.menu' . ucfirst($menuId) . 'Data', $menuData) && Config::set('m.menu.menu' . ucfirst($menuId) . 'Hierarchy', $menuHierarchy)) {
            return true;
        } else {
            return false;
        }
    }
    public static function toLink($link)
    {
        if ($link[0] != '@') {
            return $link;
        } else {
            $link = trim($link, '@');
            $linkArrOne = explode(';', $link);
            $output = array();
            foreach ($linkArrOne as $lao) {
                $linkArrTwo = explode(':', $lao);
                $output[trim($linkArrTwo[0])] = trim($linkArrTwo[1]);
            }
            return $output;
        }
    }
    public static function hrefInit($params)
    {
        $params['data'][$params['key']] = self::linkTo($params['data'][$params['key']]);
        return $params;
    }
    public static function linkTo($link)
    {
        if (is_string($link)) {
            return $link;
        } elseif (is_array($link)) {
            $output = '';
            foreach ($link as $key => $value) {
                $output = $output . $key . ':' . $value . ';';
            }
            $output = trim($output, ';');
            $output = '@' . $output;
            return $output;
        }
    }
    public static function menuData($id)
    {
        $menu = array(
            '#templates' => array(
                'menu-' . $id,
                'menu',
            ),
            'data' => Config::cacheGet('m.menu.menu_' . $id . 'Data'),
        );
        return $menu;
    }

    public static function menuHierarchy($id)
    {
        $menu = array(
            '#templates' => array(
                'menu-' . $id,
                'menu',
            ),
            'data' => Config::cacheGet('m.menu.menu_' . $id . 'Hierarchy'),
        );
        return $menu;
    }

    public static function menuRender($id)
    {
        $menu = array(
            '#templates' => array(
                'menu',
                'menu-' . $id,
            ),
            'data' => Config::cacheGet('m.menu.menu_' . $id . 'Data'),
            'hierarchy' => Config::cacheGet('m.menu.menu_' . $id . 'Hierarchy'),
        );
        return $menu;
    }
}
