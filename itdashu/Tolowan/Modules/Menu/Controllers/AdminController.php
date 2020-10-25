<?php
namespace Modules\Menu\Controllers;

use Core\Config;
use Core\Mvc\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {
        extract($this->variables['router_params']);
        $menuList = Config::get('m.menu.list', array());
        $menuData = Config::get('m.menu.menu' . ucfirst($id) . 'Data', array());
        $menuHierarchy = Config::get('m.menu.menu' . ucfirst($id) . 'Hierarchy', array());

        foreach ($menuData as $key => $value) {
            $menuData[$key]['nav'] = array(
                'editor' => array(
                    'href' => $this->url->get(array('for' => 'adminMenuLinkEditor', 'id' => $id, 'link' => $key)),
                    'name' => '编辑',
                ),
                'delete' => array(
                    'href' => $this->url->get(array('for' => 'adminMenuLinkDelete', 'id' => $id, 'link' => $key)),
                    'name' => '删除',
                ),
            );
        }
        if ($this->request->isPost() && $this->request->hasPost('rh')) {
            $rh = json_decode($this->request->getPost('rh'));
            $rh = nestableJsonToArray($rh);
            $state = Config::set('m.menu.menu' . ucfirst($id) . 'Hierarchy', $rh);
            if ($state) {
                $menuHierarchy = $rh;
                $this->flash->success('菜单排序成功');
            } else {
                $this->flash->error('菜单排序失败');
            }
        }
        $this->variables += array(
            '#templates' => 'pageNoWrapper',
            'menuId' => $id,
            'content' => array(),
        );
        $content['menuList'] = array(
            '#templates' => array(
                'box',
            ),
            'wrapper' => false,
            'id' => 'right_handle',
            'title' => $menuList[$id]['name'] . ' 链接排序',
            'max' => true,
            'color' => 'success',
            'size' => '6',
            'content' => array(
                'menuLinkSort' => array(
                    '#templates' => array(
                        'hierarchy',
                        'hierarchy-adminMenuLink',
                    ),
                    'id' => 'menuLinkHierarchy',
                    'title_display' => false,
                    'data' => $menuData,
                    'hierarchy' => $menuHierarchy,
                ),
            ),
        );

        $this->variables['content'] += $content;
    }

    public function linkAddAction()
    {
        extract($this->variables['router_params']);
        $content = array();
        $menuList = Config::get('m.menu.list');
        if (!isset($menuList[$id])) {
            return $this->notFount();
        }
        $menuData = Config::get('m.menu.menu_' . $id . 'Data');
        $menuHierarchy = Config::get('m.menu.menu_' . $id . 'Hierarchy');
        $menuLinkAddForm = Config::get('menu.menuLinkForm');
        $menuLinkAddForm['settings']['menuId'] = $id;
        $menuLinkAddForm = $this->form->create($menuLinkAddForm);
        if ($menuLinkAddForm->isValid()) {
            $menuLinkAddForm->save();
            return $this->moved(array(
                'for' => 'adminMenuLinkList',
                'id' => $id,
            ));
        }
        $this->variables = array(
            '#templates' => 'pageNoWrapper',
            'content' => array(),
        );
        $content['menuList'] = array(
            '#templates' => 'box',
            'wrapper' => false,
            'id' => 'right_handle',
            'title' => $menuList[$id]['name'] . ' 链接添加',
            'max' => true,
            'color' => 'success',
            'size' => '6',
            'content' => array(
                'menuLinkAdd' => $menuLinkAddForm->renderForm(),
            ),
        );
        $this->variables['content'] += $content;
    }

    public function linkEditorAction()
    {
        extract($this->variables['router_params']);
        $content = array();
        $menuList = Config::get('m.menu.list');
        $menuData = Config::get('m.menu.menu' . ucfirst($id) . 'Data',array());
        if(!isset($menuData[$link])){
            $this->flash->success('链接不存在');
            return $this->moved(array(
                'for' => 'adminMenuLinkList',
                'id' => $id,
            ));
        }
        $menuLinkAddForm = Config::get('menu.menuLinkForm');
        unset($menuLinkAddForm['id']);
        $menuLinkAddForm['settings']['menuId'] = $id;
        $menuLinkAddForm['settings']['id'] = $link;
        $menuLinkAddForm = $this->form->create($menuLinkAddForm, $menuData[$link]);
        if ($menuLinkAddForm->isValid()) {
            $menuLinkAddForm->save();
            return $this->moved(array(
                'for' => 'adminMenuLinkList',
                'id' => $id,
            ));
        }
        $this->variables = array(
            '#templates' => 'pageNoWrapper',
            'content' => array(),
        );
        $content['menuList'] = array(
            '#templates' => 'box',
            'wrapper' => false,
            'id' => 'right_handle',
            'title' => $menuList[$id]['name'] . ' 链接编辑',
            'max' => true,
            'color' => 'success',
            'size' => '12',
            'content' => array(
                'menuList' => $menuLinkAddForm->renderForm(),
            ),
        );
        $this->variables['content'] += $content;
    }

    public function linkDeleteAction()
    {
        extract($this->variables['router_params']);
        $menuList = Config::get('m.menu.list');
        if (!isset($menuList[$id])) {
            $this->flash->error('所要删除的菜单不存在');
        } else {
            $menuData = Config::get('m.menu.menu' . ucfirst($id) . 'Data');
            $menuHierarchy = Config::get('m.menu.menu' . ucfirst($id) . 'Hierarchy');
            if (isset($menuData[$link])) {
                unset($menuData[$link]);
            }
            $newMenuHierarchy = arrayDeleteItem($menuHierarchy, $link);
            if (Config::set('m.menu.menu' . ucfirst($id) . 'Data', $menuData) && Config::set('m.menu.menu' . ucfirst($id) . 'Hierarchy', $newMenuHierarchy)) {
                $this->flash->success('删除成功。');
            } else {
                $this->flash->error('删除失败。');
            }
        }
        return $this->moved(array(
            'for' => 'adminMenuLinkList',
            'id' => $id,
        ));
    }
}
