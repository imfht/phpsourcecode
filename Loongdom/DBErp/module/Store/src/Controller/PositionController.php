<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

namespace Store\Controller;

use Doctrine\ORM\EntityManager;
use Store\Entity\Position;
use Store\Entity\Warehouse;
use Store\Form\PositionForm;
use Store\Service\PositionManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class PositionController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $positionManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        PositionManager $positionManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->positionManager  = $positionManager;
    }

    /**
     * 仓位列表
     * @return array
     */
    public function indexAction()
    {
        $array = [];

        $page = (int) $this->params()->fromQuery('page', 1);

        $query = $this->entityManager->getRepository(Position::class)->findAllPosition();
        $array['positions'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }

    /**
     * 添加仓位
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $array = [];

        $form = new PositionForm($this->entityManager);

        $warehouses = $this->entityManager->getRepository(Warehouse::class)->findBy([], ['warehouseSort' => 'ASC']);
        $warehouseList = [];
        if($warehouses) {
            foreach ($warehouses as $value) {
                $warehouseList[$value->getWarehouseId()] = $value->getWarehouseName() . ' ['.$value->getWarehouseSn().']';
            }
        }
        $form->get('warehouseId')->setValueOptions($warehouseList);

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();

                $this->positionManager->addPosition($data, $this->adminSession('admin_id'));

                $message = sprintf($this->translator->translate('仓位 %s 添加成功！'), $data['positionSn']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('仓位'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('position');
            }
        }

        $array['form'] = $form;

        return $array;
    }

    /**
     * 编辑仓位
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $positionId = (int) $this->params()->fromRoute('id', -1);

        $positionInfo = $this->entityManager->getRepository(Position::class)->findOneByPositionId($positionId);
        if($positionInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该仓位不存在！'));
            return $this->redirect()->toRoute('position');
        }

        $form = new PositionForm($this->entityManager, $positionInfo);

        $warehouses = $this->entityManager->getRepository(Warehouse::class)->findBy([], ['warehouseSort' => 'ASC']);
        $warehouseList = [];
        if($warehouses) {
            foreach ($warehouses as $value) {
                $warehouseList[$value->getWarehouseId()] = $value->getWarehouseName() . ' ['.$value->getWarehouseSn().']';
            }
        }
        $form->get('warehouseId')->setValueOptions($warehouseList);

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();

                $this->positionManager->updatePosition($data, $positionInfo);

                $message = sprintf($this->translator->translate('仓位 %s 编辑成功！'), $data['positionSn']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('仓位'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('position');
            }
        } else $form->setData($positionInfo->valuesArray());

        return ['position' => $positionInfo, 'form' => $form];
    }

    /**
     * 仓位删除
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $positionId = (int) $this->params()->fromRoute('id', -1);

        $positionInfo = $this->entityManager->getRepository(Position::class)->findOneByPositionId($positionId);
        if($positionInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该仓位不存在！'));
            return $this->adminCommon()->toReferer();
        }

        $this->positionManager->deletePosition($positionInfo);

        $message = sprintf($this->translator->translate('仓位 %s 删除成功！'), $positionInfo->getPositionSn());
        $this->adminCommon()->addOperLog($message, $this->translator->translate('仓位'));
        $this->flashMessenger()->addSuccessMessage($message);

        return $this->adminCommon()->toReferer();
    }
}