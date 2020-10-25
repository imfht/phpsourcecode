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

namespace Stock\Controller;

use Doctrine\ORM\EntityManager;
use Stock\Entity\OtherWarehouseOrder;
use Stock\Entity\OtherWarehouseOrderGoods;
use Stock\Form\OtherOrderSearchForm;
use Stock\Form\OtherWarehouseOrderForm;
use Stock\Form\OtherWarehouseOrderGoodsForm;
use Stock\Service\OtherWarehouseOrderGoodsManager;
use Stock\Service\OtherWarehouseOrderManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $otherWarehouseOrderManager;
    private $otherWarehouseOrderGoodsManager;

    public function __construct(
        Translator $translator,
        EntityManager $entityManager,
        OtherWarehouseOrderManager $otherWarehouseOrderManager,
        OtherWarehouseOrderGoodsManager $otherWarehouseOrderGoodsManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->otherWarehouseOrderManager   = $otherWarehouseOrderManager;
        $this->otherWarehouseOrderGoodsManager = $otherWarehouseOrderGoodsManager;
    }

    /**
     * 其他入库列表
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $array= [];

        $search = [];
        $searchForm = new OtherOrderSearchForm();
        $searchForm->get('warehouse_id')->setValueOptions($this->storeCommon()->warehouseListOptions());
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }
        $array['searchForm'] = $searchForm;
        $query = $this->entityManager->getRepository(OtherWarehouseOrder::class)->findOtherWarehouseOrderList($search);
        $array['orderList'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }

    /**
     * 添加入库
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $goodsForm  = new OtherWarehouseOrderGoodsForm($this->entityManager);
        $form       = new OtherWarehouseOrderForm($this->entityManager);

        $form->get('warehouseId')->setValueOptions($this->storeCommon()->warehouseListOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            $goodsForm->setData($data);
            if($form->isValid() && $goodsForm->isValid()) {
                $data = $form->getData();
                $goodsData = $goodsForm->getData();

                $this->entityManager->beginTransaction();
                try {
                    $otherWarehouseOrder = $this->otherWarehouseOrderManager->addOtherWarehouseOrder($data, $goodsData, $this->adminSession('admin_id'));
                    $this->otherWarehouseOrderGoodsManager->addOtherWarehouseOrderGoods($goodsData, $data['warehouseId'], $otherWarehouseOrder->getOtherWarehouseOrderId());

                    $this->getEventManager()->trigger('other-warehouse-order.insert.post', $this, $otherWarehouseOrder);

                    $this->entityManager->commit();

                    $message = $otherWarehouseOrder->getWarehouseOrderSn() . $this->translator->translate('其他入库成功！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('其他入库'));
                    $this->flashMessenger()->addSuccessMessage($message);
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('其他入库失败！'));
                }
                return $this->redirect()->toRoute('erp-stock');
            }

        } else $form->get('warehouseOrderSn')->setValue($this->stockCommon()->createOtherWarehouseOrderSn());

        return ['form' => $form, 'goodsForm' => $goodsForm];
    }

    /**
     * 查看其他入库订单信息
     * @return array|\Zend\Http\Response
     */
    public function viewAction()
    {
        $otherWarehouseOrderId  = (int) $this->params()->fromRoute('id', -1);
        $otherWarehouseOrderInfo= $this->entityManager->getRepository(OtherWarehouseOrder::class)->findOneBy(['otherWarehouseOrderId' => $otherWarehouseOrderId]);
        if($otherWarehouseOrderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该入库单不存在！'));
            return $this->redirect()->toRoute('erp-stock');
        }

        $orderGoods = $this->entityManager->getRepository(OtherWarehouseOrderGoods::class)->findBy(['otherWarehouseOrderId' => $otherWarehouseOrderId]);

        return ['otherWarehouseOrder' => $otherWarehouseOrderInfo, 'orderGoods' => $orderGoods];
    }
}