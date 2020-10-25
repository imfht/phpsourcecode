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
use Purchase\Entity\WarehouseOrder;
use Sales\Entity\SalesSendWarehouseGoods;
use Store\Entity\Warehouse;
use Store\Form\WarehouseForm;
use Store\Service\WarehouseManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class WarehouseController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $warehouseManager;

    public function __construct(
        Translator          $translator,
        EntityManager       $entityManager,
        WarehouseManager    $warehouseManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->warehouseManager = $warehouseManager;
    }

    /**
     * 仓库列表
     * @return array
     */
    public function indexAction()
    {
        $array = [];

        $page = (int) $this->params()->fromQuery('page', 1);

        $query = $this->entityManager->getRepository(Warehouse::class)->findAllWarehouse();
        $array['warehouses'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }

    /**
     * 仓库添加
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $array = [];

        $form = new WarehouseForm($this->entityManager);

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();

                $this->warehouseManager->addWarehouse($data, $this->adminSession('admin_id'));

                $message = sprintf($this->translator->translate('仓库 %s 添加成功！'), $data['warehouseName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('仓库'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('warehouse');
            }
        }

        $array['form'] = $form;
        return $array;
    }

    /**
     * 编辑仓库
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $warehouseId = (int) $this->params()->fromRoute('id', -1);

        $warehouseInfo = $this->entityManager->getRepository(Warehouse::class)->findOneByWarehouseId($warehouseId);
        if($warehouseInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该仓库不存在！'));
            return $this->redirect()->toRoute('warehouse');
        }

        $form = new WarehouseForm($this->entityManager, $warehouseInfo);

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();

                $this->warehouseManager->updateWarehouse($warehouseInfo, $data);

                $message = sprintf($this->translator->translate('仓库 %s 编辑成功！'), $warehouseInfo->getWarehouseName());
                $this->adminCommon()->addOperLog($message, $this->translator->translate('仓库'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('warehouse');
            }
        } else $form->setData($warehouseInfo->valuesArray());

        return ['warehouse' => $warehouseInfo, 'form' => $form];
    }

    /**
     * 仓库批量处理
     * @return \Zend\Http\Response
     */
    public function updateAllAction()
    {
        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            if(!empty($data['select_id']) and !empty($data['editAllState'])) {
                $this->warehouseManager->updateAllWarehouse($data);

                $message = $this->translator->translate('批量处理成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('仓库'));
                $this->flashMessenger()->addSuccessMessage($message);
            }
        }
        return $this->redirect()->toRoute('warehouse');
    }

    /**
     * 删除仓库
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $warehouseId = (int) $this->params()->fromRoute('id', -1);

        $warehouseInfo = $this->entityManager->getRepository(Warehouse::class)->findOneByWarehouseId($warehouseId);
        if($warehouseInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该仓库不存在！'));
            return $this->adminCommon()->toReferer();
        }

        $oneWarehouseOrder = $this->entityManager->getRepository(WarehouseOrder::class)->findOneByWarehouseId($warehouseId);
        $oneSendWarehouse  = $this->entityManager->getRepository(SalesSendWarehouseGoods::class)->findOneByWarehouseId($warehouseId);
        if($oneWarehouseOrder || $oneSendWarehouse) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该仓库已经被使用，不能删除！'));
            return $this->adminCommon()->toReferer();
        }

        $delState = $this->warehouseManager->deleteWarehouse($warehouseInfo);

        if($delState) {
            $message = sprintf($this->translator->translate('仓库 %s 删除成功！'), $warehouseInfo->getWarehouseName());
            $this->adminCommon()->addOperLog($message, $this->translator->translate('仓库'));
            $this->flashMessenger()->addSuccessMessage($message);
        }
        else $this->flashMessenger()->addErrorMessage(sprintf($this->translator->translate('仓库 %s 删除失败！该仓库下面还有仓位'), $warehouseInfo->getWarehouseName()));

        return $this->adminCommon()->toReferer();
    }
}