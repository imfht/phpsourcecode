<?php
namespace Modules\Entity\Controllers;

use Core\Config;
use Core\Mvc\Controller;
use Modules\Queue\Library\ImagesToLocal;
use Modules\Queue\Models\Queue as QueueModel;
use Core\File;
use Core\HttpClient;

class AdminController extends Controller
{

    public function indexAction()
    {
        extract($this->variables['router_params']);


        // 获取实体类型
        $entityObject = $this->entityManager->get($entity);

        $entityInfo = $entityObject->getEntityInfo();
        if (!isset($entityInfo['path']['adminEntityList']) || $entityInfo['path']['adminEntityList'] === false) {
            return $this->notFount();
        }
        $contentModelList = $entityObject->getContentModelList();
        $label = $entityInfo['entityName'];
        $filterForm = $entityObject->filterForm($entity);
        $query = array(
            'all' => true,
            'limit' => 20,
            'page' => $page,
            'paginator' => true,
            'order' => 'changed DESC',
        );
        $handleForm = $entityObject->handleForm();
        if($handleForm->isValid()){
            $entityObject->handleSubmit($handleForm);
        }
        if ($filterForm->isValid()) {
            $query = $entityObject->submitFilterForm($filterForm, $query);
        }
        $data = $entityObject->find($query);
        $this->variables = array_merge($this->variables, array(
            'title' => '实体',
            'contentModelList' => $contentModelList,
            'description' => $label . '列表',
            'params' => $this->variables['router_params'],
            'breadcrumb' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '控制台',
                ),
                'nodeList' => array(
                    'name' => $label . '列表',
                ),
            ),
            'content' => array(),
        ));

        if ($filterForm) {
            $content['filter'] = array(
                '#templates' => 'box',
                'max' => false,
                'color' => 'widget',
                'hiddenTitle' => false,
                'size' => '12',
                'wrapper' => true,
                'content' => array(
                    'filterForm' => $filterForm->renderForm(),
                ),
            );
        }
        $menuGroup = $entityObject->menuTabs();
        $content['nodeList'] = array();
        $content['nodeList'] = array(
            '#templates' => 'box',
            'wrapper' => true,
            'title' => $label . '列表',
            'max' => false,
            'color' => 'success',
            'size' => '12',
            'content' => array(
                'menuGroup' => array(),
                'list' => array(
                    '#templates' => array(
                        'adminEntityList',
                        'adminEntityList-' . $entity,
                    ),
                    '#module' => $entityInfo['module'],
                    'data' => $data,
                    'handleForm' => $handleForm,
                ),
            ),
        );
        if ($menuGroup) {
            $content['nodeList']['content']['menuGroup'] = array(
                '#templates' => 'menuGroup',
                'title' => '添加',
                'data' => $menuGroup,
            );
        }
        $this->variables['content'] += $content;
    }

    public function addAction()
    {
        extract($this->variables['router_params']);
        $entityManager = $this->entityManager->get($entity);
        $contentModelInfo = $entityManager->getContentModelInfo($contentModel);
        $entityInfo = $entityManager->getEntityInfo();
        if (!isset($entityInfo['path']['adminEntityAdd']) || $entityInfo['path']['adminEntityAdd'] === false) {
            return $this->notFount();
        }
        $label = '添加' . $contentModelInfo['modelName'];
        $content = array();

        $entityEditForm = $entityManager->addForm($contentModel);
        if($entityManager->isSaveSuccess() === true){
            return $this->moved(array(
                'for' => 'adminEntityList',
                'entity' => $entity,
                'page' => 1
            ));
        }
        $this->variables = array(
            'title' => $label,
            'description' => '',
            'breadcrumb' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '控制台',
                ),
                'adminAddNode' => array(
                    'name' => $label,
                ),
            ),
            'content' => array(
                'entityForm' => array(
                    '#templates' => 'adminEntityForm',
                    'data' => $entityEditForm,
                ),
            ),
        );
    }

    public function editAction()
    {
        extract($this->variables['router_params']);
        $entityManager = $this->entityManager->get($entity);
        $contentModelInfo = $entityManager->getContentModelInfo($contentModel);
        $entityInfo = $entityManager->getEntityInfo();
        if (!isset($entityInfo['path']['adminEntityEdit']) || $entityInfo['path']['adminEntityEdit'] === false) {
            return $this->notFount();
        }
        $label = '编辑' . $contentModelInfo['modelName'];
        $content = array();

        $entityEditForm = $entityManager->editForm($contentModel, $id);
        if($entityManager->isSaveSuccess() === true){
            return $this->moved(array(
                'for' => 'adminEntityList',
                'entity' => $entity,
                'page' => 1
            ));
        }
        $this->variables = array(
            'title' => $label,
            'description' => '',
            'breadcrumb' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '控制台',
                ),
                'adminAddNode' => array(
                    'name' => $label,
                ),
            ),
            'content' => array(
                'entityForm' => array(
                    '#templates' => 'adminEntityForm',
                    'data' => $entityEditForm,
                ),
            ),
        );
    }

    public function deleteAction()
    {
        extract($this->variables['router_params']);
        $entityModel = $this->entityManager->get($entity);
        $entityInfo = $entityModel->getEntityInfo();
        if (!isset($entityInfo['path']['adminEntityDelete']) || $entityInfo['path']['adminEntityDelete'] === false) {
            return $this->notFount();
        }
        if ($entityModel->delete($id)) {
            $this->flash->success('删除成功');
        } else {
            $this->flash->error('删除失败');
        }
        return $this->moved(array('for' => 'adminEntityList', 'entity' => $entity, 'page' => 1));
    }
}
