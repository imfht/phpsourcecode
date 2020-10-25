<?php
namespace Modules\Config\Controllers;

use Core\Config;
use Core\EntityManager;
use Core\Mvc\Controller;
use Core\HttpClient;
use Core\HttpClient\Streams;

class AdminController extends Controller
{

    /**
     *
     * @param
     *            $contentModel
     */
    public function configListAction()
    {
        extract($this->variables['router_params']);
        $entity = $this->entityManager->get('configList');
        $entityEditForm = $entity->addForm($contentModel);
        $content = array();
        $contentModelInfo = $entity->getContentModelInfo($contentModel);
        $label = $contentModelInfo['modelName'];
        $data = $entity->find(array('contentModel' => $contentModel));
        $this->variables['title'] = $contentModelInfo['modelName'];
        $this->variables['description'] = $contentModelInfo['description'];
        $this->variables += array(
            'id' => $contentModel,
            '#templates' => 'page',
            'breadcrumb' => array(
                'module' => array(
                    'name' => $contentModelInfo['modelName'],
                ),
            ),
            'content' => array(),
        );
        $content['ConfigList'] = array(
            '#templates' => 'box',
            'title' => $label.'列表',
            'max' => false,
            'color' => 'primary',
            'size' => '6',
            'wrapper' => true,
            'content' => array(
                'dataList' => array(
                    '#templates' => array(
                        'configListTable',
                        'configListTable-' . $contentModel,
                    ),
                    '#module' => $contentModelInfo['module'],
                    'theadDisplay' => true,
                    'thead' => $entity->getThead($contentModel),
                    'checkAll' => false,
                    'data' => $data,
                ),
            ),
        );
        $content['termHandle'] = array(
            '#templates' => array(
                'box',
                'box-right',
            ),
            'id' => 'right_handle',
            'title' => '添加'.$label,
            'wrapper' => true,
            'max' => true,
            'color' => 'success',
            'size' => '6',
            'content' => array(
                'termList' => $entityEditForm->renderForm(),
            ),
        );

        $this->variables['content'] += $content;
    }

    public function ConfigListEditorAction()
    {
        extract($this->variables['router_params']);
        $entity = $this->entityManager->get('configList');
        $contentModelInfo = $entity->getContentModelInfo($contentModel);
        $entityEditForm = $entity->editForm($contentModel, $id);
        if ($entityEditForm->isValid()) {
            if ($entity->save($entityEditForm)) {
                $this->flash->success('保存成功');
            }
            return $this->moved(array(
                'for' => 'adminConfigList',
                'contentModel' => $contentModel,
            ));
        }
        $content = array();
        $this->variables['title'] = '编辑 ' . $contentModelInfo['modelName'];
        $this->variables['description'] = $contentModelInfo['description'];
        $this->variables += array(
            'id' => $id,
            'contentModel' => $contentModel,
            '#templates' => 'pageNoWrapper',
            'breadcrumb' => array(
                'module' => array(
                    'name' => $contentModelInfo['modelName'],
                ),
            ),
            'content' => array(),
        );

        $content['termHandle'] = array(
            '#templates' => array(
                'box',
                'box-right',
            ),
            'id' => 'right_handle',
            'title' => '编辑 ' . $contentModelInfo['modelName'],
            'wrapper' => false,
            'max' => true,
            'color' => 'success',
            'size' => '12',
            'content' => array(
                'termList' => $entityEditForm->renderForm(),
            ),
        );

        $this->variables['content'] += $content;
    }

    public function ConfigListDeleteAction()
    {
        extract($this->variables['router_params']);
        $entity = $this->entityManager->get('configList');
        $configList = $entity->findFirst(array(
            'contentModel' => $contentModel,
            'id' => $id
        ),true);
        if($configList && $configList->delete()){
            $this->flash->success('删除成功');
        }else{
            $this->flash->error('删除失败');
        }
        return $this->moved(array(
            'for' => 'adminConfigList',
            'contentModel' => $contentModel,
        ));
    }

    public function configAction()
    {
        extract($this->variables['router_params']);
        $entity = $this->entityManager->get('config');
        $content = array();
        $data = $entity->find();
        $this->variables['title'] = '配置';
        $this->variables['description'] = '';
        $this->variables += array(
            '#templates' => 'page',
            'breadcrumb' => array(
                'module' => array(
                    'name' => '配置',
                ),
            ),
            'content' => array(),
        );
        $content['ConfigList'] = array(
            '#templates' => 'box',
            'title' => '列表',
            'max' => false,
            'color' => 'primary',
            'size' => '12',
            'wrapper' => true,
            'content' => array(
                'dataList' => array(
                    '#templates' => array(
                        'configTable',
                    ),
                    '#module' => 'config',
                    'theadDisplay' => true,
                    'thead' => $entity->getThead(),
                    'checkAll' => false,
                    'data' => $data,
                ),
            ),
        );

        $this->variables['content'] += $content;
    }

    public function configEditAction()
    {
        extract($this->variables['router_params']);
        $entity = $this->entityManager->get('config');
        $contentModelInfo = $entity->getContentModelInfo($contentModel);
        $entityEditForm = $entity->editForm($contentModel, $contentModel);
        $content = array();
        $this->variables['title'] = $contentModelInfo['modelName'];
        $this->variables['description'] = $contentModelInfo['description'];
        $this->variables += array(
            'contentModel' => $contentModel,
            '#templates' => 'page',
            'breadcrumb' => array(
                'configList' => array(
                    'href' => array(
                        'for' => 'adminConfig'
                    ),
                    'name' => '配置'
                ),
                'configEdit' => array(
                    'name' => $contentModelInfo['modelName'],
                ),
            ),
            'content' => array(),
        );

        $content['termHandle'] = array(
            '#templates' => array(
                'box',
                'box-right',
            ),
            'id' => 'right_handle',
            'title' => '编辑 ' . $contentModelInfo['modelName'],
            'wrapper' => true,
            'max' => true,
            'color' => 'success',
            'size' => '12',
            'content' => array(
                'termList' => $entityEditForm->renderForm(),
            ),
        );

        $this->variables['content'] += $content;
    }
}
