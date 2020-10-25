<?php
namespace Modules\Region\Controllers;

use Core\Config;
use Core\Mvc\Controller;

use Core\Fun;

class AdminController extends Controller
{

    public function indexAction()
    {
        extract($this->variables['router_params']);
        $regionList = Config::get('m.region.list');
        $entity = $this->entityManager->get('block');
        $data = $entity->getContentModelList();
        $this->variables += array(
            '#templates' => array(
                'box',
            ),
            'wrapper' => false,
            'id' => 'right_handle',
            'title' => '添加' . $regionList[$region]['name'] . '区块--选择区块类型',
            'max' => true,
            'color' => 'success',
            'size' => '6',
            'content' => array(
                'termList' => array(
                    '#templates' => 'blockType',
                    'region' => $region,
                    'data' => $data
                ),
            ),
        );
    }

    public function sortAction()
    {
        extract($this->variables['router_params']);
        $regionList = Config::get('m.region.list');
        $entity = $this->entityManager->get('block');
        $data = $entity->find(array('region' => $region));

        if ($this->request->isPost() && $this->request->hasPost('rh')) {
            $rh = json_decode($this->request->getPost('rh'));
            $data['hierarchy'] = nestableJsonToArray($rh);
            $state = Config::set('m.region.blockList-' . $region, $data);
            if ($state) {
                $this->flash->success('区域区块排序成功');
            } else {
                $this->flash->error('区域区块排序失败');
            }
        }
        $this->variables += array(
            '#templates' => 'pageNoWrapper',
            'region' => $region,
            'content' => array(),
        );
        $content['regionList'] = array(
            '#templates' => 'box',
            'wrapper' => false,
            'id' => 'right_handle',
            'title' => $regionList[$region]['name'] . ' 区块排序',
            'max' => true,
            'color' => 'success',
            'size' => '6',
            'content' => array(
                'regionBlockSort' => array(
                    '#templates' => array('hierarchical', 'hierarchical-regionBlockSort'),
                    'id' => 'regionBlockSort',
                    'title_display' => false,
                    'data' => isset($data['data']) && is_array($data['data']) ? $data['data'] : array(),
                    'hierarchy' => isset($data['hierarchy']) && is_array($data['hierarchy']) ? $data['hierarchy'] : array(),
                ),
            ),
        );

        $this->variables['content'] += $content;
    }

    public function addAction()
    {
        extract($this->variables['router_params']);
        $content = array();
        $regionList = Config::get('m.region.list');
        $entity = $this->entityManager->get('block');
        $editForm = $entity->addForm($contentModel, array('region' => $region, 'contentModel' => $contentModel));
        if (false) {
            return $this->moved(array('for' => 'adminRegionBlockSort', 'region' => $region));
        }
        $this->variables += array(
            '#templates' => 'pageNoWrapper',
            'content' => array(),
        );
        $content['regionList'] = array(
            '#templates' => array(
                'box',
            ),
            'wrapper' => false,
            'id' => 'right_handle',
            'title' => '添加',
            'max' => true,
            'color' => 'success',
            'size' => '6',
            'content' => array(
                'termList' => $editForm->renderForm(),
            ),
        );
        $this->variables['content'] += $content;
    }

    public function editAction()
    {
        extract($this->variables['router_params']);
        $content = array();
        $regionList = Config::get('m.region.list');
        $entity = $this->entityManager->get('block');
        $editForm = $entity->editForm($contentModel, array('region' => $region, 'block' => $block));
        if (false) {
            return $this->moved(array('for' => 'adminRegionBlockSort', 'region' => $region));
        }
        $this->variables += array(
            '#templates' => 'pageNoWrapper',
            'content' => array(),
        );
        $content['regionList'] = array(
            '#templates' => array(
                'box',
            ),
            'wrapper' => false,
            'id' => 'right_handle',
            'title' => '添加',
            'max' => true,
            'color' => 'success',
            'size' => '6',
            'content' => array(
                'termList' => $editForm->renderForm(),
            ),
        );
        $this->variables['content'] += $content;
    }

    public function blockEditorAction()
    {
        extract($this->variables['router_params']);
        $content = array();
        $regionList = Config::get('m.region.region', array());
        if (!isset($regionList[$region])) {
            $this->flash->error('区域不存在');
            return $this->moved(array(
                'for' => 'adminRegionBlockSort',
                'region' => $region,
            ));
        }
        $regionData = Config::get('m.region.region' . ucfirst($region) . 'Data');
        if (!isset($regionData['data'][$block])) {
            $this->flash->error('区块 ' . $block . ' 不存在');
            return $this->moved(array(
                'for' => 'adminRegionBlockSort',
                'region' => $region,
            ));
        }
        $blockType = Config::cache('blockType');
        $regionBlockAddForm = Config::get($blockType[$regionData['data'][$block]['blockType']]['entity']);
        $regionBlockAddForm['settings']['region'] = $region;
        $regionBlockAddForm['settings']['blockType'] = $regionData['data'][$block]['blockType'];
        $regionBlockAddForm['settings']['id'] = $block;
        $regionBlockAddForm = new Form($regionBlockAddForm, $regionData['data'][$block]);
        if ($regionBlockAddForm->isValid()) {
            $state = $regionBlockAddForm->save();
            if ($state == true) {
                return $this->moved(array(
                    'for' => 'adminRegionBlockSort',
                    'region' => $region,
                ));
            }
        }
        $this->variables = array(
            '#templates' => 'pageNoWrapper',
            'content' => array(),
        );
        $content['regionList'] = array(
            '#templates' => 'box',
            'title' => '【' . $regionList[$region]['name'] . '】区块<' . $regionData['data'][$block]['name'] . '>编辑',
            'wrapper' => false,
            'id' => 'right_handle',
            'max' => true,
            'color' => 'success',
            'size' => '6',
            'content' => array(
                'regionList' => $regionBlockAddForm->renderForm(),
            ),
        );
        $this->variables['content'] += $content;
    }

    public function blockDeleteAction()
    {
        extract($this->variables['router_params']);
        $regionBlockList = Config::get('m.region.blockList-' . $region);
        if (isset($regionBlockList['data'][$block])) {
            unset($regionBlockList['data'][$block]);
        }
        $regionBlockList['hierarchy'] = arrayDeleteItem($regionBlockList['hierarchy'], $block);
        if (Config::set('m.region.blockList-' . $region, $regionBlockList)) {
            $this->flash->success('删除成功。');
        } else {
            $this->flash->error('删除失败。');
        }

        return $this->moved(array(
            'for' => 'adminRegionBlockSort',
            'region' => $region,
        ));
    }
}
