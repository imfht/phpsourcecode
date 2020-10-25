<?php
namespace Core;

class Menu{
    public static function deleteHierarchy($hierarchy,$item){
        if (isset($hierarchy[$item])){
            unset($hierarchy[$item]);
            return $hierarchy;
        }
        foreach ($hierarchy as $key => $value){
            if (is_array($value)){
                $hierarchy[$key] = self::deleteHierarchy($value, $item);
            }
        }
    }
    public static function deleteMenu($data, $hierarchy, $item, $depth = 1) {
        if ($depth > 3) {
            return false;
        }
        foreach ($hierarchy as $key => $value) {
            if (is_array($value)) {
                $depth++;
                $hierarchy[$key] = self::deleteMenu($data, $value, $item, $depth);
                if ($hierarchy[$key] == false) {
                    if (is_array($hierarchy[$key])) {
                        $hierarchy[$key] = $key;
                    } else {
                        return false;
                    }
    
                }
            }
            if (!isset($data[$key])) {
                if (is_array($hierarchy[$key])) {
                    foreach ($value as $vk => $vv) {
                        $hierarchy[$vk] = $vv;
                    }
                }
                unset($hierarchy[$key]);
            }
        }
        return $hierarchy;
        if ($depth > 3) {
            return false;
        }
        if (isset($hierarchy[$item])) {
            if (!is_string($hierarchy[$item])) {
                foreach ($hierarchy[$item] as $key => $value) {
                    $hierarchy[$key] = $value;
                }
            }
            unset($hierarchy[$item]);
            return $hierarchy;
        } else {
            $depth++;
            foreach ($hierarchy as $hk => $hv) {
                $hierarchy[$hk] = self::deleteMenu($hv, $item, $depth);
                if ($hierarchy[$hk] == false) {
                    return false;
                }
            }
        }
        return $hierarchy;
    }
    public function sortAction($menu) {
    
        $content = array();
        $menuData = Options::get('menu_' . $menu . 'Data');
        $menuHierarchy = Options::get('menu_' . $menu . 'Hierarchy');
        if ($this->request->isPost() && $this->request->hasPost('rh')) {
            $hierarchy = json_decode($this->request->getPost('rh'), true);
            $menuHierarchy = Hierarchy::handle($hierarchy);
            $menuData = Hierarchy::data($menuHierarchy, $menuData);
            if ($menuHierarchy == false) {
                $this->flash->error('保存失败，菜单最多只能有三层。');
            } else {
                if (Options::set('menu_' . $menu . 'Hierarchy', $menuHierarchy) && Options::set('menu_' . $menu . 'Data', $menuData)) {
                    $this->flash->success('排序更新成功。');
                } else {
                    $this->flash->error('排序更新失败。');
                }
            }
            //echo $this->request->getPost('rh');
        }
        $this->variables['title'] = '菜单排序';
        $this->variables['page'] = array(
            '#theme' => array(
                'name' => 'pageNoWrapper',
            ),
            'content' => array(),
        );
        // 添加编辑菜单
        foreach ($menuData as $mk => $mv) {
            $menuData[$mk]['nav'] = array(
                'editor' => array(
                    'href' => $this->url->get(array('for' => 'adminMenuEditor', 'menu' => $menu, 'item' => $mk)),
                    'name' => '编辑',
                ),
                'delete' => array(
                    'href' => $this->url->get(array('for' => 'adminMenuDelete', 'menu' => $menu, 'item' => $mk)),
                    'name' => '删除',
                ),
            );
        }
        $content['menuList'] = array(
            '#theme' => array(
                'name' => 'boxNoWrapper',
            ),
            'title' => '菜单排序',
            'max' => false,
            'color' => 'blue2',
            'size' => '12',
            'content' => array(
                'menuList' => array(
                    '#theme' => array(
                        'name' => 'hierarchical',
                    ),
                    'id' => 'menu_hierarchical' . $menu,
                    'title_display' => false,
                    'data' => $menuData,
                    'hierarchy' => $menuHierarchy,
                ),
            ),
        );
        $this->variables['page']['content'] += $content;
    }
    public function hierarchyHandle($hierarchy, $depth = 1) {
        if ($depth > 3) {
            return false;
        }
        $output = array();
        foreach ($hierarchy as $key => $value) {
            if (isset($value['children'])) {
                $depth++;
                $output[$value['id']] = $this->hierarchyHandle($value['children'], $depth);
                if ($output[$value['id']] == false) {
                    return false;
                }
            } else {
                $output[$value['id']] = $value['id'];
            }
        }
        return $output;
    }
}