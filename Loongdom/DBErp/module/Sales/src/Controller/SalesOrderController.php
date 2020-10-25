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

namespace Sales\Controller;

use Admin\Data\Common;
use Doctrine\ORM\EntityManager;
use Sales\Entity\SalesOperLog;
use Sales\Entity\SalesOrder;
use Sales\Entity\SalesOrderGoods;
use Sales\Form\SalesOrderForm;
use Sales\Form\SalesOrderGoodsForm;
use Sales\Form\SearchSalesOrderForm;
use Sales\Form\SendOrderForm;
use Sales\Service\SalesGoodsPriceLogManager;
use Sales\Service\SalesOrderGoodsManager;
use Sales\Service\SalesOrderManager;
use Sales\Service\SalesSendOrderManager;
use Sales\Service\SalesSendWarehouseGoodsManager;
use Store\Entity\WarehouseGoods;
use Store\Service\GoodsManager;
use Store\Service\WarehouseGoodsManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;

class SalesOrderController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $salesOrderManager;
    private $salesOrderGoodsManager;
    private $salesSendOrderManager;
    private $salesSendWarehouseGoodsManager;
    private $warehouseGoodsManager;
    private $goodsManager;
    private $salesGoodsPriceLogManager;

    public function __construct(
        Translator              $translator,
        EntityManager           $entityManager,
        SalesOrderManager       $salesOrderManager,
        SalesOrderGoodsManager  $salesOrderGoodsManager,
        SalesSendOrderManager   $salesSendOrderManager,
        SalesSendWarehouseGoodsManager  $salesSendWarehouseGoodsManager,
        WarehouseGoodsManager   $warehouseGoodsManager,
        GoodsManager            $goodsManager,
        SalesGoodsPriceLogManager $salesGoodsPriceLogManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->salesOrderManager= $salesOrderManager;
        $this->salesOrderGoodsManager   = $salesOrderGoodsManager;
        $this->salesSendOrderManager    = $salesSendOrderManager;
        $this->salesSendWarehouseGoodsManager = $salesSendWarehouseGoodsManager;
        $this->warehouseGoodsManager    = $warehouseGoodsManager;
        $this->goodsManager     = $goodsManager;
        $this->salesGoodsPriceLogManager= $salesGoodsPriceLogManager;
    }

    /**
     * 销售订单列表
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $array= [];

        $search = [];
        $searchForm = new SearchSalesOrderForm();
        $searchForm->get('customer_id')->setValueOptions($this->customerCommon()->customerListOption());
        $searchForm->get('receivables_code')->setValueOptions(Common::receivable($this->translator));
        $searchForm->get('return_state')->setValueOptions(Common::existReturn($this->translator));
        $salesOrderStateArray = Common::salesOrderState($this->translator);
        unset($salesOrderStateArray[-5], $salesOrderStateArray[-1]);
        $searchForm->get('sales_order_state')->setValueOptions($salesOrderStateArray);
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }
        $array['searchForm'] = $searchForm;

        $query = $this->entityManager->getRepository(SalesOrder::class)->findAllSalesOrder($search);
        $array['orderList'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }

    /**
     * 添加销售订单
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new SalesOrderForm($this->entityManager);
        $goodsForm = new SalesOrderGoodsForm($this->entityManager);

        $form->get('receivablesCode')->setValueOptions(Common::receivable($this->translator));
        $form->get('customerId')->setValueOptions($this->customerCommon()->customerListOption());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            $goodsForm->setData($data);
            if($form->isValid() && $goodsForm->isValid()) {
                $data = $form->getData();
                $goodsData = $goodsForm->getData();

                $this->entityManager->beginTransaction();
                try {
                    $salesOrderInfo = $this->salesOrderManager->addSalesOrder($data, $goodsData, $this->adminSession('admin_id'));
                    $this->salesOrderGoodsManager->addSalesOrderGoods($goodsData, $salesOrderInfo->getSalesOrderId());
                    $this->salesCommon()->addSalesOperLog($salesOrderInfo->getSalesOrderState(), $salesOrderInfo->getSalesOrderId());

                    $this->entityManager->commit();

                    $message = $salesOrderInfo->getSalesOrderSn() . $this->translator->translate('销售订单添加成功！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('销售订单'));
                    $this->flashMessenger()->addSuccessMessage($message);
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('销售订单添加失败！'));
                }
                return $this->redirect()->toRoute('sales-order');
            }
        } else $form->get('salesOrderSn')->setValue($this->salesCommon()->createSalesOrderSn());

        return ['form' => $form, 'goodsForm' => $goodsForm];
    }

    /**
     * 编辑采购订单
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $salesOrderId = (int) $this->params()->fromRoute('id', -1);
        $salesOrderInfo = $this->entityManager->getRepository(SalesOrder::class)->findOneBySalesOrderId($salesOrderId);
        if($salesOrderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该订单不存在！'));
            return $this->redirect()->toRoute('sales-order');
        }
        if($salesOrderInfo->getSalesOrderState() != 0) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该订单所属状态不能被编辑！'));
            return $this->redirect()->toRoute('sales-order');
        }

        $form = new SalesOrderForm($this->entityManager, $salesOrderInfo);
        $goodsForm = new SalesOrderGoodsForm($this->entityManager);

        $form->get('receivablesCode')->setValueOptions(Common::receivable($this->translator));
        $form->get('customerId')->setValueOptions($this->customerCommon()->customerListOption());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            $goodsForm->setData($data);
            if($form->isValid() && $goodsForm->isValid()) {
                $data = $form->getData();
                $goodsData = $goodsForm->getData();

                $this->entityManager->beginTransaction();
                try {
                    $this->salesOrderManager->updateSalesOrder($data, $goodsData, $salesOrderInfo);
                    $this->salesOrderGoodsManager->editSalesOrderGoods($goodsData, $salesOrderId);

                    $this->entityManager->commit();

                    $message = $salesOrderInfo->getSalesOrderSn() . $this->translator->translate('销售订单编辑成功！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('销售订单'));
                    $this->flashMessenger()->addSuccessMessage($message);
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('销售订单编辑失败！'));
                }
                return $this->redirect()->toRoute('sales-order');
            }
        } else $form->setData($salesOrderInfo->valuesArray());

        $salesOrderGoods = $this->entityManager->getRepository(SalesOrderGoods::class)->findBy(['salesOrderId' => $salesOrderId], ['salesGoodsId' => 'ASC']);

        return ['form' => $form, 'goodsForm' =>$goodsForm, 'salesOrderInfo' => $salesOrderInfo, 'salesOrderGoods' => $salesOrderGoods];
    }

    /**
     * 查看销售订单
     * @return array|\Zend\Http\Response
     */
    public function viewAction()
    {
        $salesOrderId = (int) $this->params()->fromRoute('id', -1);
        $salesOrderInfo = $this->entityManager->getRepository(SalesOrder::class)->findOneBySalesOrderId($salesOrderId);
        if($salesOrderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该订单不存在！'));
            return $this->redirect()->toRoute('sales-order');
        }

        $salesOrderGoods = $this->entityManager->getRepository(SalesOrderGoods::class)->findBy(['salesOrderId' => $salesOrderId], ['salesGoodsId' => 'ASC']);

        $salesOperLog = $this->entityManager->getRepository(SalesOperLog::class)->findBy(['salesOrderId' => $salesOrderId]);

        return ['orderInfo' => $salesOrderInfo, 'orderGoods' => $salesOrderGoods, 'salesOperLog' => $salesOperLog];
    }

    /**
     * 销售订单删除
     * @return mixed
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $salesOrderId = (int) $this->params()->fromRoute('id', -1);
        $salesOrderInfo = $this->entityManager->getRepository(SalesOrder::class)->findOneBy(['salesOrderId' => $salesOrderId, 'salesOrderState' => 0]);
        if($salesOrderInfo) {
            $this->entityManager->beginTransaction();
            try {
                $this->salesOrderManager->deleteSalesOrder($salesOrderInfo);
                $this->salesOrderGoodsManager->deleteMoreSalesOrderIdGoods($salesOrderId);
                $this->salesCommon()->delSalesOperLog($salesOrderId);

                $this->entityManager->commit();

                $message = $salesOrderInfo->getSalesOrderSn() . $this->translator->translate('销售订单删除成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('销售订单'));
                $this->flashMessenger()->addSuccessMessage($message);
            } catch (\Exception $e) {
                $this->entityManager->rollback();
                $this->flashMessenger()->addWarningMessage($this->translator->translate('销售订单删除失败！'));
            }
        }

        return $this->adminCommon()->toReferer();
    }

    /**
     * 确认销售订单
     * @return mixed
     */
    public function confirmSalesOrderAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $salesOrderId = (int) $this->params()->fromRoute('id', -1);
        $salesOrderInfo = $this->entityManager->getRepository(SalesOrder::class)->findOneBy(['salesOrderId' => $salesOrderId, 'salesOrderState' => 0]);
        if($salesOrderInfo == null) $this->flashMessenger()->addWarningMessage($this->translator->translate('确认采购单失败！'));
        else {
            $this->salesOrderManager->updateSalesOrderState(1, $salesOrderInfo);
            $this->salesCommon()->addSalesOperLog(1, $salesOrderId);

            $message = $salesOrderInfo->getSalesOrderSn() . $this->translator->translate('销售订单确认完成！');
            $this->adminCommon()->addOperLog($message, $this->translator->translate('销售订单'));
            $this->flashMessenger()->addSuccessMessage($message);
        }

        return $this->adminCommon()->toReferer();
    }

    /**
     * 取消确认销售订单
     * @return mixed
     */
    public function cancelSalesOrderAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $salesOrderId = (int) $this->params()->fromRoute('id', -1);
        $salesOrderInfo = $this->entityManager->getRepository(SalesOrder::class)->findOneBy(['salesOrderId' => $salesOrderId, 'salesOrderState' => 1]);
        if($salesOrderInfo == null) $this->flashMessenger()->addWarningMessage($this->translator->translate('取消确认采购单失败！'));
        else {
            $this->salesOrderManager->updateSalesOrderState(0, $salesOrderInfo);
            $this->salesCommon()->addSalesOperLog(0, $salesOrderId);

            $message = $salesOrderInfo->getSalesOrderSn() . $this->translator->translate('销售订单取消确认完成！');
            $this->adminCommon()->addOperLog($message, $this->translator->translate('销售订单'));
            $this->flashMessenger()->addSuccessMessage($message);
        }

        return $this->adminCommon()->toReferer();
    }

    /**
     * 订单发货出库
     * @return array|\Zend\Http\Response
     */
    public function sendOrderAction()
    {
        $salesOrderId = (int) $this->params()->fromRoute('id', -1);
        $salesOrderInfo = $this->entityManager->getRepository(SalesOrder::class)->findOneBySalesOrderId($salesOrderId);
        if($salesOrderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该订单不存在！'));
            return $this->redirect()->toRoute('sales-order');
        }

        $salesOrderGoods    = $this->entityManager->getRepository(SalesOrderGoods::class)->findBy(['salesOrderId' => $salesOrderId], ['salesGoodsId' => 'ASC']);
        $form = new SendOrderForm($this->entityManager, $salesOrderGoods);
        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();
                $this->entityManager->beginTransaction();
                try {
                    $sendOrder = $this->salesSendOrderManager->addSalesSendOrder($data, $salesOrderInfo, $this->adminSession('admin_id'));
                    $sendArray = $this->salesSendWarehouseGoodsManager->checkAndReturnSendWarehouseGoodsNum($data, $salesOrderGoods);

                    $sendWarehouseGoodsState = $this->warehouseGoodsManager->outWarehouseGoodsStock($sendArray['warehouseGoods']);
                    $this->salesSendWarehouseGoodsManager->addSalesSendWarehouseGoods($sendArray['warehouseGoods'], $sendOrder);
                    $this->salesGoodsPriceLogManager->addSalesGoodsPriceLog($salesOrderGoods, $salesOrderId, time());
                    $goodsState = $this->goodsManager->outGoodsStock($sendArray['goods']);

                    if(!$sendWarehouseGoodsState || !$goodsState) $this->entityManager->rollback();

                    $this->salesOrderManager->updateSalesOrderState(6, $salesOrderInfo);
                    $this->salesCommon()->addSalesOperLog(6, $salesOrderId);
                    $this->entityManager->commit();

                    $message = $salesOrderInfo->getSalesOrderSn() . $this->translator->translate('销售订单发货成功！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('销售订单'));
                    $this->flashMessenger()->addSuccessMessage($message);
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('销售订单发货失败！'));
                }
                return $this->redirect()->toRoute('sales-order');
            }
        } else $form->get('sendOrderSn')->setValue($this->salesCommon()->createSendOrderSn());

        //商品有库存的仓库信息
        $warehouseArray     = [];
        if($salesOrderGoods) {
            foreach ($salesOrderGoods as $orderGoodsValue) {
                $warehouseArray[$orderGoodsValue->getGoodsId()] = $this->entityManager->getRepository(WarehouseGoods::class)->findBy(['goodsId' => $orderGoodsValue->getGoodsId()], ['warehouseGoodsStock'=> 'DESC']);
            }
        }

        return ['form' => $form, 'orderInfo' => $salesOrderInfo, 'orderGoods' => $salesOrderGoods, 'warehouse' => $warehouseArray];
    }

    /**
     * 删除销售单中的商品
     * @return JsonModel
     */
    public function delSalesOrderGoodsAction()
    {
        $array = ['state' => 'false'];

        $goodsId = (int) $this->request->getPost('goodsId', 0);
        $orderId = (int) $this->request->getPost('orderId', 0);
        if($orderId > 0 && $goodsId > 0) {
            $salesOrderInfo = $this->entityManager->getRepository(SalesOrder::class)->findOneBy(['salesOrderId' => $orderId, 'salesOrderState' => 0]);
            if($salesOrderInfo) {
                $salesOrderGoodsInfo = $this->entityManager->getRepository(SalesOrderGoods::class)->findOneBy(['salesOrderId' => $orderId, 'goodsId' => $goodsId]);
                if($salesOrderGoodsInfo) {
                    $orderUpdate = [
                        'salesOrderGoodsAmount' => $salesOrderInfo->getSalesOrderGoodsAmount() - $salesOrderGoodsInfo->getSalesGoodsPrice() * $salesOrderGoodsInfo->getSalesGoodsSellNum(),
                        'salesOrderTaxAmount'   => $salesOrderInfo->getSalesOrderTaxAmount() - $salesOrderGoodsInfo->getSalesGoodsTax(),
                        'salesOrderAmount'      => $salesOrderInfo->getSalesOrderAmount() - $salesOrderGoodsInfo->getSalesGoodsAmount()
                    ];
                    $this->entityManager->beginTransaction();
                    try {
                        $this->salesOrderManager->updateSalesOrderAmount($orderUpdate, $salesOrderInfo);
                        $this->salesOrderGoodsManager->deleteSalesOrderGoods($salesOrderGoodsInfo);
                        $this->entityManager->commit();
                        $array['state'] = 'ok';
                        $this->adminCommon()->addOperLog($salesOrderInfo->getSalesOrderSn() . $this->translator->translate('订单，删除商品') . '：' . $salesOrderGoodsInfo->getGoodsName(), $this->translator->translate('销售订单'));
                    } catch (\Exception $e) {
                        $this->entityManager->rollback();
                    }
                }
            }
        }
        return new JsonModel($array);
    }
}