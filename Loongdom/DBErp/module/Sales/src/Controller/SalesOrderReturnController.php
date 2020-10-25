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
use Sales\Entity\SalesOrderGoodsReturn;
use Sales\Entity\SalesOrderReturn;
use Sales\Entity\SalesSendOrder;
use Sales\Form\SalesOrderReturnForm;
use Sales\Form\SearchSalesOrderReturnForm;
use Sales\Service\SalesOrderGoodsManager;
use Sales\Service\SalesOrderGoodsReturnManager;
use Sales\Service\SalesOrderManager;
use Sales\Service\SalesOrderReturnManager;
use Sales\Service\SalesSendOrderManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class SalesOrderReturnController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $salesOrderReturnManager;
    private $salesOrderGoodsReturnManager;
    private $salesOrderManager;
    private $salesOrderGoodsManager;
    private $salesSendOrderManager;

    public function __construct(
        Translator              $translator,
        EntityManager           $entityManager,
        SalesOrderReturnManager         $salesOrderReturnManager,
        SalesOrderGoodsReturnManager    $salesOrderGoodsReturnManager,
        SalesOrderManager       $salesOrderManager,
        SalesOrderGoodsManager  $salesOrderGoodsManager,
        SalesSendOrderManager   $salesSendOrderManager
    )
    {
        $this->translator                   = $translator;
        $this->entityManager                = $entityManager;
        $this->salesOrderReturnManager      = $salesOrderReturnManager;
        $this->salesOrderGoodsReturnManager = $salesOrderGoodsReturnManager;
        $this->salesOrderManager            = $salesOrderManager;
        $this->salesOrderGoodsManager       = $salesOrderGoodsManager;
        $this->salesSendOrderManager        = $salesSendOrderManager;
    }

    /**
     * 销售退货列表
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $array= [];

        $search = [];
        $searchForm = new SearchSalesOrderReturnForm();
        $searchForm->get('customer_id')->setValueOptions($this->customerCommon()->customerListOption());
        $searchForm->get('return_state')->setValueOptions(Common::salesOrderReturnState($this->translator));
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }
        $array['searchForm'] = $searchForm;

        $query = $this->entityManager->getRepository(SalesOrderReturn::class)->findAllSalesOrderReturn($search);
        $array['orderList'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }

    /**
     * 添加退货
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $sendOrderId    = (int) $this->params()->fromRoute('id', -1);
        $sendOrderInfo = $this->entityManager->getRepository(SalesSendOrder::class)->findOneBySendOrderId($sendOrderId);
        if($sendOrderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该发货单不存在！'));
            return $this->redirect()->toRoute('sales-order-return');
        }
        if($sendOrderInfo->getOneSalesOrder()->getSalesOrderState() != 6) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('当前状态不能执行该操作'));
            return $this->redirect()->toRoute('sales-order-return');
        }

        $form = new SalesOrderReturnForm($this->entityManager, $sendOrderInfo->getSalesOrderId());
        $formData = [];
        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();

                $this->entityManager->beginTransaction();
                try {
                    $salesOrderReturn   = $this->salesOrderReturnManager->addSalesOrderReturn($data, $sendOrderInfo, $this->adminSession('admin_id'));
                    $returnArray        = $this->salesOrderGoodsReturnManager->addSalesOrderGoodsReturn($data, $salesOrderReturn);
                    $this->salesOrderGoodsManager->updateSalesOrderGoodsNumAndAmountSub($returnArray['salesGoods']);
                    $this->salesOrderReturnManager->updateSalesOrderReturnAmount($returnArray['goodsReturnAmount'], $returnArray['returnAmount'], $salesOrderReturn);
                    $this->salesOrderManager->updateSalesOrderAmount(
                        [
                            'salesOrderGoodsAmount' => $sendOrderInfo->getOneSalesOrder()->getSalesOrderGoodsAmount() - $returnArray['goodsReturnAmount'],
                            'salesOrderAmount'      => $sendOrderInfo->getOneSalesOrder()->getSalesOrderAmount() - $returnArray['returnAmount']
                        ],
                        $sendOrderInfo->getOneSalesOrder());
                    $this->salesOrderManager->updateSalesOrderReturnState(1, $sendOrderInfo->getOneSalesOrder());
                    $this->salesSendOrderManager->updateSalesSendOrderReturnState(1, $sendOrderInfo);
                    $this->salesCommon()->addSalesOperLog(-1, $sendOrderInfo->getSalesOrderId());

                    $this->entityManager->commit();

                    $message = $this->translator->translate('销售单') . $sendOrderInfo->getOneSalesOrder()->getSalesOrderSn() . $this->translator->translate('退货添加完成！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('销售退货'));
                    $this->flashMessenger()->addSuccessMessage($message);

                    return $this->redirect()->toRoute('sales-order-return');
                } catch (\Exception $e) {
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('退货添加失败！'));
                    return $this->redirect()->toRoute('sales-order-return');
                }


            } else $formData = $data;
        }

        $salesOrderGoods = $this->entityManager->getRepository(SalesOrderGoods::class)->findBy(['salesOrderId' => $sendOrderInfo->getOneSalesOrder()->getSalesOrderId()], ['salesGoodsId' => 'ASC']);

        return ['form' => $form, 'formData' => $formData, 'sendOrderInfo' => $sendOrderInfo, 'orderGoods' => $salesOrderGoods];
    }

    /**
     * 销售退货单详情
     * @return array
     */
    public function viewAction()
    {
        $returnId = (int) $this->params()->fromRoute('id', -1);
        $returnInfo = $this->entityManager->getRepository(SalesOrderReturn::class)->findOneBySalesOrderReturnId($returnId);
        if($returnInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该销售退货单不存在！'));
            return $this->adminCommon()->toReferer();
        }

        $orderGoods = $this->entityManager->getRepository(SalesOrderGoodsReturn::class)->findBy(['salesOrderReturnId' => $returnId]);
        return ['returnInfo' => $returnInfo, 'orderGoods' => $orderGoods];
    }

    /**
     * 销售退货完成
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function finishAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $returnId = (int) $this->params()->fromRoute('id', -1);
        $returnInfo = $this->entityManager->getRepository(SalesOrderReturn::class)->findOneBySalesOrderReturnId($returnId);
        if ($returnInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该销售退货单不存在！'));
        }
        if($returnInfo->getReturnState() != -1) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该销售退货不能执行完成操作！'));
        } else {
            $this->salesOrderReturnManager->updateSalesOrderReturnState(-5, $returnInfo, ['finishTime' => time()]);
            $this->salesCommon()->addSalesOperLog(-5, $returnInfo->getSalesOrderId());

            $message = $this->translator->translate('销售单') . $returnInfo->getSalesOrderSn() . $this->translator->translate('退货操作完成！');
            $this->adminCommon()->addOperLog($message, $this->translator->translate('销售退货'));
            $this->flashMessenger()->addSuccessMessage($message);
        }

        return $this->adminCommon()->toReferer();
    }

    /**
     * 取消退货
     * @return mixed
     */
    public function cancelAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $returnId = (int) $this->params()->fromRoute('id', -1);
        $returnInfo = $this->entityManager->getRepository(SalesOrderReturn::class)->findOneBySalesOrderReturnId($returnId);
        if ($returnInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该销售退货单不存在！'));
        }
        if($returnInfo->getReturnState() != -1) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该销售退货不能执行取消操作！'));
        } else {
            $this->entityManager->beginTransaction();
            try {
                $returnGoods = $this->entityManager->getRepository(SalesOrderGoodsReturn::class)->findBy(['salesOrderReturnId' => $returnId]);
                if($returnGoods) {
                    foreach ($returnGoods as $goodsValue) {
                        $this->salesOrderGoodsManager->updateSalesOrderGoodsNumAndAmountAdd(
                            [
                                'salesOrderId' => $returnInfo->getSalesOrderId(),
                                'salesGoodsId' => $goodsValue->getSalesGoodsId(),
                                'goodsReturnNum' => $goodsValue->getGoodsReturnNum(),
                                'goodsReturnAmount' => $goodsValue->getGoodsReturnAmount()
                            ]
                        );
                        $this->salesOrderGoodsReturnManager->deleteSalesOrderGoodsRetrun($goodsValue);
                    }

                    $salesOrderInfo = $this->entityManager->getRepository(SalesOrder::class)->findOneBySalesOrderId($returnInfo->getSalesOrderId());
                    $this->salesOrderManager->updateSalesOrderAmount(
                        [
                            'salesOrderGoodsAmount' => $salesOrderInfo->getSalesOrderGoodsAmount() + $returnInfo->getSalesOrderGoodsReturnAmount(),
                            'salesOrderAmount'      => $salesOrderInfo->getSalesOrderAmount() + $returnInfo->getSalesOrderReturnAmount()
                        ],
                        $salesOrderInfo);
                    $this->salesOrderReturnManager->deleteSalesOrderReturn($returnInfo);
                    $this->salesCommon()->delSalesOperLog($salesOrderInfo->getSalesOrderId(), true);

                    $oneReturnInfo = $this->entityManager->getRepository(SalesOrderReturn::class)->findOneBy(['salesOrderId' => $returnInfo->getSalesOrderId()]);
                    if($oneReturnInfo == null) {
                        $this->salesOrderManager->updateSalesOrderReturnState(0, $salesOrderInfo);
                        $this->salesSendOrderManager->updateSalesSendOrderReturnState(0, $this->entityManager->getRepository(SalesSendOrder::class)->findOneBySendOrderId($returnInfo->getSalesSendOrderId()));
                    }
                }
                $this->entityManager->commit();

                $message = $this->translator->translate('销售单') . $returnInfo->getSalesOrderSn() . $this->translator->translate('取消退货成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('销售退货'));
                $this->flashMessenger()->addSuccessMessage($message);
            } catch (\Exception $e) {
                $this->flashMessenger()->addWarningMessage($this->translator->translate('取消退货失败！'));
                $this->entityManager->rollback();
            }
        }

        return $this->adminCommon()->toReferer();
    }
}