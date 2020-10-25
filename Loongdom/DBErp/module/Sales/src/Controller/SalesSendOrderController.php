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
use Sales\Entity\SalesOrder;
use Sales\Entity\SalesOrderGoods;
use Sales\Entity\SalesSendOrder;
use Sales\Entity\SalesSendWarehouseGoods;
use Sales\Form\SearchSendOrderForm;
use Sales\Service\SalesOrderManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class SalesSendOrderController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $salesOrderManager;

    public function __construct(
        Translator          $translator,
        EntityManager       $entityManager,
        SalesOrderManager   $salesOrderManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->salesOrderManager= $salesOrderManager;
    }

    /**
     * 销售发货单列表
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $array= [];

        $search = [];
        $searchForm = new SearchSendOrderForm();
        $searchForm->get('customer_id')->setValueOptions($this->customerCommon()->customerListOption());
        $searchForm->get('receivables_code')->setValueOptions(Common::receivable($this->translator));
        $searchForm->get('return_state')->setValueOptions(Common::existReturn($this->translator));
        $salesOrderStateArray = Common::salesOrderState($this->translator);
        $searchForm->get('sales_order_state')->setValueOptions([6 => $salesOrderStateArray[6], 12 => $salesOrderStateArray[12]]);
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }
        $array['searchForm'] = $searchForm;

        $query = $this->entityManager->getRepository(SalesSendOrder::class)->findAllSendOrder($search);
        $array['orderSendList'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }

    /**
     * 销售发货单详情
     * @return array|\Zend\Http\Response
     */
    public function viewAction()
    {
        $sendOrderId = (int) $this->params()->fromRoute('id', -1);
        $sendOrderInfo = $this->entityManager->getRepository(SalesSendOrder::class)->findOneBySendOrderId($sendOrderId);
        if($sendOrderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该发货单不存在！'));
            return $this->redirect()->toRoute('sales-send-order');
        }

        $sendWarehouseArray = [];
        $sendWarehouse      = $this->entityManager->getRepository(SalesSendWarehouseGoods::class)->findBy(['sendOrderId' => $sendOrderId]);
        foreach ($sendWarehouse as $warehouseValue) {
            $sendWarehouseArray[$warehouseValue->getGoodsId()][] = [
                'warehouseName' => $warehouseValue->getOneWarehouse()->getWarehouseName(),
                'goodsNum'      => $warehouseValue->getSendGoodsStock()
            ];
        }

        $salesOrderGoods    = $this->entityManager->getRepository(SalesOrderGoods::class)->findBy(['salesOrderId' => $sendOrderInfo->getSalesOrderId()], ['salesGoodsId' => 'ASC']);

        return ['sendOrderInfo' => $sendOrderInfo, 'orderGoods' => $salesOrderGoods, 'sendWarehouse' => $sendWarehouseArray];
    }

    /**
     * 销售订单确认收货
     * @return mixed
     */
    public function finishSalesOrderAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $sendOrderId    = (int) $this->params()->fromRoute('id', -1);
        $sendOrderInfo = $this->entityManager->getRepository(SalesSendOrder::class)->findOneBySendOrderId($sendOrderId);
        if($sendOrderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该发货单不存在！'));
            return $this->adminCommon()->toReferer();
        }
        if($sendOrderInfo->getOneSalesOrder()->getSalesOrderState() != 6) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('当前状态不能执行该操作'));
            return $this->adminCommon()->toReferer();
        }

        $this->salesOrderManager->updateSalesOrderState(12,
            $this->entityManager->getRepository(SalesOrder::class)->findOneBySalesOrderId($sendOrderInfo->getSalesOrderId()));
        $this->salesCommon()->addSalesOperLog(12, $sendOrderInfo->getSalesOrderId());

        $message = $this->translator->translate('销售单') . $sendOrderInfo->getOneSalesOrder()->getSalesOrderSn() . $this->translator->translate('订单确认收货完成！');
        $this->adminCommon()->addOperLog($message, $this->translator->translate('销售退货'));
        $this->flashMessenger()->addSuccessMessage($message);

        $this->getEventManager()->trigger('send-order.finish.post', $this, $sendOrderInfo);

        $this->adminCommon()->toReferer();
    }
}