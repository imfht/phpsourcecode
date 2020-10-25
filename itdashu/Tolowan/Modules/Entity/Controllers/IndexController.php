<?php
namespace Modules\Entity\Controllers;

use Core\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        extract($this->variables['router_params']);
        $entityObject = $this->entityManager->get($entity);
        $entityInfo = $entityObject->getEntityInfo();
        if (!isset($entityInfo['path']['entity']) || $entityInfo['path']['entity'] === false) {
            return $this->notFount();
        }
        $data = $entityObject->findFirst($id, true);
        if(!$data || $data->created > time()){
            return $this->notFount();
        }
        $this->variables['data'] = $data;
        $this->variables['title'] = $data->getTitle();
        $this->variables['#templates'] = array(
            'pageEntity',
            'pageEntity' . ucfirst($entity),
            'pageEntity' . ucfirst($entity) . ucfirst($entityObject->contentModel),
            'pageEntity' . ucfirst($entity) . $id,
        );
    }

    public function entityListAction($entityType)
    {
        extract($this->variables['router_params']);
        $entity = $this->entityManager->get($entity);
        $entityInfo = $entity->getEntityInfo();
        if (!isset($entityInfo['path']['entityList']) || $entityInfo['path']['entityList'] === false) {
            return $this->notFount();
        }
        $query = array(
            'limit' => 15,
            'paginator' => true,
            'page' => $page,
        );
        $data = $entity->find($query);
        $this->variables += array(
            '#templates' => array(
                'pageEntityList',
                'pageEntityList-' . $entity,
            ),
            'data' => $data,
            'entity' => $entity,
            'page' => $page,
        );
    }

    public function entityModelListAction()
    {
        extract($this->variables['router_params']);
        $entity = $this->entityManager->get($entity);
        $entityInfo = $entity->getEntityInfo();
        if (!isset($entityInfo['path']['entityContentModelList']) || $entityInfo['path']['entityContentModelList'] === false) {
            return $this->notFount();
        }
        $query = array(
            'andWhere' => array(
                array(
                    'conditions' => '%contentModel% = :contentModel:',
                    'bind' => array('contentModel' => $contentModel),
                ),
            ),
            'limit' => 15,
            'paginator' => true,
            'page' => $page,
        );
        $data = $entity->find($query);
        $this->variables += array(
            '#templates' => array(
                'pageEntityTypeList',
                'pageEntityTypeList-' . $entity,
                'pageEntityTypeList-' . $entity . '-' . $contentModel,
            ),
            'data' => $data,
            'entity' => $entity,
            'page' => $page,
        );
    }

    public function entityModelFieldListAction()
    {
        extract($this->variables['router_params']);
    }
}
