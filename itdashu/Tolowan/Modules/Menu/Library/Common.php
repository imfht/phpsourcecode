<?php
namespace Modules\Menu\Library;

use Core\Options;

class Common
{

    public static function saveMenu($formEntity)
    {
        global $di;
        $formData = $formEntity['settings']['formData'];
        $menuList = Options::get('menuList');
        if (isset($formEntity['settings']['id'])) {
            $id = $formEntity['settings']['id'];
            $menuList[$id] = array(
                'id' => $id,
                'name' => $formData['name'],
                'description' => $formData['description'],
                'other' => isset($formData['other']) ? $formData['other'] : null,
            );
        } else {
            $menuList[$formData['machine']] = array(
                'id' => $formData['machine'],
                'name' => $formData['name'],
                'description' => $formData['description'],
                'other' => isset($formData['other']) ? $formData['other'] : null,
            );
        }
        if (!isset($formEntity['settings']['id'])) {
            $dataSave = Options::create('menu_' . $formData['machine'] . 'Data', array());
            $hierarchySave = Options::create('menu_' . $formData['machine'] . 'Hierarchy', array());
        } else {
            $dataSave = true;
            $hierarchySave = true;
        }
        $dataSave = Options::create('menu_' . $formData['machine'] . 'Data', array());
        $hierarchySave = Options::create('menu_' . $formData['machine'] . 'Hierarchy', array());
        $menuListSave = Options::set('menuList', $menuList);
        if ($menuListSave && $dataSave && $hierarchySave) {
            $di->getShared('flash')->success('菜单保存成功');
            return true;
        } else {
            $di->getShared('flash')->error('菜单保存失败');
            return false;
        }
    }

    public static function saveLink($formEntity)
    {
        global $di;
        $formData = $formEntity['settings']['formData'];
        $menuId = $formEntity['settings']['menuId'];
        $menuData = Options::get('menu_' . $menuId . 'Data');
        $menuHierarchy = Options::get('menu_' . $menuId . 'Hierarchy');
        if (isset($formEntity['settings']['id'])) {
            $id = $formEntity['settings']['id'];
            $menuData[$id] = array(
                'name' => $formData['name'],
                'description' => $formData['description'],
                'href' => self::toLink($formData['href']),
            );
        } else {
            $menuData[$formData['machine']] = array(
                'name' => $formData['name'],
                'description' => $formData['description'],
                'href' => self::toLink($formData['href']),
            );
            $menuHierarchy[$formData['machine']] = $formData['machine'];
        }
        if (Options::set('menu_' . $menuId . 'Data', $menuData) && Options::set('menu_' . $menuId . 'Hierarchy', $menuHierarchy)) {
            $di->getShared('flash')->success('保存成功。');
            return true;
        } else {
            $di->getShared('flash')->error('保存失败。');
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
            'data' => Options::cacheGet('menu_' . $id . 'Data'),
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
            'data' => Options::cacheGet('menu_' . $id . 'Hierarchy'),
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
            'data' => Options::cacheGet('menu_' . $id . 'Data'),
            'hierarchy' => Options::cacheGet('menu_' . $id . 'Hierarchy'),
        );
        return $menu;
    }
}
