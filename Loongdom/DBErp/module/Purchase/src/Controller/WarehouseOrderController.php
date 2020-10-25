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

namespace Purchase\Controller;

use Admin\Data\Common;
use Doctrine\ORM\EntityManager;
use Purchase\Entity\Order;
use Purchase\Entity\OrderGoods;
use Purchase\Entity\WarehouseOrder;
use Purchase\Entity\WarehouseOrderGoods;
use Purchase\Form\SearchWarehouseOrderForm;
use Purchase\Form\WarehouseOrderForm;
use Purchase\Service\OrderGoodsManager;
use Purchase\Service\OrderManager;
use Purchase\Service\PurchaseGoodsPriceLogManager;
use Purchase\Service\WarehouseOrderGoodsManager;
use Purchase\Service\WarehouseOrderManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;

class WarehouseOrderController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $orderManager;
    private $orderGoodsManager;
    private $warehouseOrderManager;
    private $warehouseOrderGoodsManager;
    private $purchaseGoodsPriceLogManager;

    public function __construct(
        Translator          $translator,
        EntityManager       $entityManager,
        OrderManager        $orderManager,
        OrderGoodsManager   $orderGoodsManager,
        WarehouseOrderManager $warehouseOrderManager,
        WarehouseOrderGoodsManager $warehouseOrderGoodsManager,
        PurchaseGoodsPriceLogManager $purchaseGoodsPriceLogManager
    )
    {
        $this->translator           = $translator;
        $this->entityManager        = $entityManager;
        $this->orderManager         = $orderManager;
        $this->orderGoodsManager    = $orderGoodsManager;
        $this->warehouseOrderManager= $warehouseOrderManager;
        $this->warehouseOrderGoodsManager = $warehouseOrderGoodsManager;
        $this->purchaseGoodsPriceLogManager = $purchaseGoodsPriceLogManager;
    }

    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $array= [];

        $search = [];
        $searchForm = new SearchWarehouseOrderForm();
        $searchForm->get('supplier_id')->setValueOptions($this->customerCommon()->supplierListOptions());
        $searchForm->get('payment_code')->setValueOptions(Common::payment($this->translator));
        $pOrderStateArray = Common::purchaseOrderState($this->translator);
        $searchForm->get('p_order_state')->setValueOptions([2 => $pOrderStateArray[2], 3 => $pOrderStateArray[3]]);
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }
        $array['searchForm'] = $searchForm;

        $query = $this->entityManager->getRepository(WarehouseOrder::class)->findWarehouseOrderList($search);
        $array['orderList'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }

    /**
     * 验货入库
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        //采购单id
        $orderId    = (int) $this->params()->fromRoute('id', -1);
        $orderInfo  = $this->entityManager->getRepository(Order::class)->findOneBy(['pOrderId' => $orderId, 'pOrderState' => 1]);
        if($orderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该订单状态不符或者订单不存在！'));
            return $this->redirect()->toRoute('p-order');
        }
        //采购订单中的商品
        $orderGoods = $this->entityManager->getRepository(OrderGoods::class)->findBy(['pOrderId' => $orderId]);

        $form = new WarehouseOrderForm($this->entityManager);

        $form->get('warehouseId')->setValueOptions($this->storeCommon()->warehouseListOptions());
        $form->get('warehouseOrderState')->setValueOptions(Common::warehouseOrderState($this->translator));

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();

                $this->entityManager->beginTransaction();
                try {
                    $warehouseOrder = $this->warehouseOrderManager->addWarehouseOrder($data, $orderInfo, $this->adminSession('admin_id'));
                    if($data['warehouseOrderState'] == 3) {//当直接入库时，进行此处理
                        $this->warehouseOrderGoodsManager->addWarehouseOrderGoods($orderGoods, $warehouseOrder);
                        $this->purchaseGoodsPriceLogManager->addPurchaseGoodsPriceLog($orderGoods, $orderId, time());
                    }
                    $this->orderManager->updateOrderState(['pOrderState' => $data['warehouseOrderState']], $orderInfo);
                    $this->purchaseCommon()->addPurchaseOperLog($data['warehouseOrderState'], $orderId);

                    $this->getEventManager()->trigger('warehouse-order.add.post', $this, $warehouseOrder);

                    $this->entityManager->commit();

                    $message = $warehouseOrder->getWarehouseOrderSn() . $this->translator->translate('验货入库成功！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('采购入库'));
                    $this->flashMessenger()->addSuccessMessage($message);
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('验货入库失败！'));
                    return $this->redirect()->toRoute('p-order');
                }
                return $this->redirect()->toRoute('warehouse-order');
            }
        } else $form->get('warehouseOrderSn')->setValue($this->purchaseCommon()->createWarehouseOrderSn());

        return ['form' => $form, 'orderInfo' => $orderInfo, 'orderGoods' => $orderGoods];
    }

    /**
     *
     * @return array|\Zend\Http\Response
     */
    public function viewAction()
    {
        //入库单id
        $warehouseOrderId = (int) $this->params()->fromRoute('id', -1);
        $warehouseOrderInfo = $this->entityManager->getRepository(WarehouseOrder::class)->findOneBy(['warehouseOrderId' => $warehouseOrderId]);
        if($warehouseOrderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该入库单不存在！'));
            return $this->redirect()->toRoute('warehouse-order');
        }

        $orderInfo = $this->entityManager->getRepository(Order::class)->findOneBy(['pOrderId' => $warehouseOrderInfo->getPOrderId()]);

        if($warehouseOrderInfo->getWarehouseOrderState() == 3)
            $orderGoods = $this->entityManager->getRepository(WarehouseOrderGoods::class)->findBy(['warehouseOrderId' => $warehouseOrderInfo->getWarehouseOrderId()]);
        else
            $orderGoods = $this->entityManager->getRepository(OrderGoods::class)->findBy(['pOrderId' => $warehouseOrderInfo->getPOrderId()]);

        return ['warehouseOrder' => $warehouseOrderInfo, 'orderGoods' => $orderGoods, 'orderInfo' => $orderInfo];
    }

    /**
     * 待入库单入库
     * @return \Zend\Http\Response
     */
    public function insertWarehouseAction()
    {
        //入库单id
        $warehouseOrderId = (int) $this->params()->fromRoute('id', -1);
        $warehouseOrderInfo = $this->entityManager->getRepository(WarehouseOrder::class)->findOneBy(['warehouseOrderId' => $warehouseOrderId, 'warehouseOrderState' => 2]);
        if($warehouseOrderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该入库单状态不符或者入库单不存在！'));
            return $this->adminCommon()->toReferer();
        }

        $this->entityManager->beginTransaction();
        try {
            $orderGoods = $this->entityManager->getRepository(OrderGoods::class)->findBy(['pOrderId' => $warehouseOrderInfo->getPOrderId()]);
            $warehouseOrder = $this->warehouseOrderManager->updateWarehouseOrderState(3, $warehouseOrderInfo);
            $this->warehouseOrderGoodsManager->addWarehouseOrderGoods($orderGoods, $warehouseOrder);
            $this->purchaseGoodsPriceLogManager->addPurchaseGoodsPriceLog($orderGoods, $warehouseOrderInfo->getPOrderId(), time());

            $orderInfo  = $this->entityManager->getRepository(Order::class)->findOneBy(['pOrderId' => $warehouseOrderInfo->getPOrderId(), 'pOrderState' => 2]);
            $this->orderManager->updateOrderState(['pOrderState' => 3], $orderInfo);
            $this->purchaseCommon()->addPurchaseOperLog(3, $warehouseOrderInfo->getPOrderId());

            $this->getEventManager()->trigger('warehouse-order.insert.post', $this, $warehouseOrder);

            $this->entityManager->commit();

            $message = $warehouseOrderInfo->getWarehouseOrderSn() . $this->translator->translate('采购入库成功！');
            $this->adminCommon()->addOperLog($message, $this->translator->translate('采购入库'));
            $this->flashMessenger()->addSuccessMessage($message);
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->flashMessenger()->addWarningMessage($this->translator->translate('采购入库失败！'));
        }

        return $this->adminCommon()->toReferer();
    }

    /**
     * 删除待入库单
     * @return \Zend\Http\Response|JsonModel
     */
    public function deleteAction()
    {
        $array = ['state' => 'false'];

        if(!$this->adminCommon()->validatorCsrf()) {
            return new JsonModel($array);
        }

        //入库单id
        $warehouseOrderId = (int) $this->params()->fromRoute('id', -1);
        $warehouseOrderInfo = $this->entityManager->getRepository(WarehouseOrder::class)->findOneBy(['warehouseOrderId' => $warehouseOrderId, 'warehouseOrderState' => 2]);
        if($warehouseOrderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该入库单状态不符或者入库单不存在！'));
            return new JsonModel($array);
        }

        $this->entityManager->beginTransaction();
        try {
            $this->warehouseOrderManager->deleteWarehouseOrder($warehouseOrderInfo);
            $orderInfo  = $this->entityManager->getRepository(Order::class)->findOneBy(['pOrderId' => $warehouseOrderInfo->getPOrderId(), 'pOrderState' => 2]);
            $this->orderManager->updateOrderState(['pOrderState' => 1], $orderInfo);
            $this->purchaseCommon()->addPurchaseOperLog(1, $warehouseOrderInfo->getPOrderId());

            $this->entityManager->commit();
            $array = ['state' => 'ok'];

            $message = $warehouseOrderInfo->getWarehouseOrderSn() . $this->translator->translate('待入库单取消成功！');
            $this->adminCommon()->addOperLog($message, $this->translator->translate('采购入库'));
            $this->flashMessenger()->addSuccessMessage($message);
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->flashMessenger()->addWarningMessage($this->translator->translate('待入库单取消失败！'));
        }

        return new JsonModel($array);
    }
}