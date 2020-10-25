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
use Purchase\Entity\OrderGoodsReturn;
use Purchase\Entity\OrderReturn;
use Purchase\Form\OrderGoodsReturnForm;
use Purchase\Form\SearchOrderReturnForm;
use Purchase\Service\OrderGoodsManager;
use Purchase\Service\OrderGoodsReturnManager;
use Purchase\Service\OrderManager;
use Purchase\Service\OrderReturnManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

/**
 * 退货订单
 * Class OrderReturnController
 * @package Purchase\Controller
 */
class OrderReturnController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $orderReturnManager;
    private $orderGoodsReturnManager;
    private $orderGoodsManager;
    private $orderManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        OrderManager    $orderManager,
        OrderGoodsManager   $orderGoodsManager,
        OrderReturnManager  $orderReturnManager,
        OrderGoodsReturnManager $orderGoodsReturnManager
    )
    {
        $this->translator   = $translator;
        $this->entityManager= $entityManager;
        $this->orderReturnManager   = $orderReturnManager;
        $this->orderGoodsReturnManager = $orderGoodsReturnManager;
        $this->orderGoodsManager    = $orderGoodsManager;
        $this->orderManager = $orderManager;
    }

    /**
     * 采购退货单列表
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $array= [];

        $search = [];
        $searchForm = new SearchOrderReturnForm();
        $searchForm->get('supplier_id')->setValueOptions($this->customerCommon()->supplierListOptions());
        $searchForm->get('return_state')->setValueOptions(Common::salesOrderReturnState($this->translator));
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }
        $array['searchForm'] = $searchForm;

        $query = $this->entityManager->getRepository(OrderReturn::class)->findAllOrderReturn($search);
        $array['orderList'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }

    /**
     * 查看采购退货详情
     * @return array|\Zend\Http\Response
     */
    public function viewAction()
    {
        $returnId = (int) $this->params()->fromRoute('id', -1);
        $returnInfo = $this->entityManager->getRepository(OrderReturn::class)->findOneByOrderReturnId($returnId);
        if($returnInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该采购退货单不存在！'));
            return $this->adminCommon()->toReferer();
        }

        $orderGoods = $this->entityManager->getRepository(OrderGoodsReturn::class)->findBy(['orderReturnId' => $returnId]);
        return ['returnInfo' => $returnInfo, 'orderGoods' => $orderGoods];
    }

    /**
     * 退货完成处理
     * @return \Zend\Http\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function returnFinishAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $returnId = (int) $this->params()->fromRoute('id', -1);
        $returnInfo = $this->entityManager->getRepository(OrderReturn::class)->findOneByOrderReturnId($returnId);
        if($returnInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该采购退货单不存在！'));
        }
        if($returnInfo->getReturnState() != -1) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该采购退货不能执行完成操作！'));
        } else {
            $finishTime = time();
            $this->orderReturnManager->updateOrderReturnState(-5, $returnInfo, ['finishTime' => $finishTime]);
            $this->purchaseCommon()->addPurchaseOperLog(-5, $returnInfo->getPOrderId(), $finishTime);

            $message = $returnInfo->getPOrderSn() . $this->translator->translate('退货操作完成！');
            $this->adminCommon()->addOperLog($message, $this->translator->translate('采购退货'));
            $this->flashMessenger()->addSuccessMessage($message);
        }

        return $this->adminCommon()->toReferer();
    }

    /**
     * 取消退货
     * @return \Zend\Http\Response
     */
    public function cancelAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $returnId = (int) $this->params()->fromRoute('id', -1);
        $returnInfo = $this->entityManager->getRepository(OrderReturn::class)->findOneByOrderReturnId($returnId);
        if($returnInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该采购退货单不存在！'));
        }
        if($returnInfo->getReturnState() != -1) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该采购退货不能执行取消操作！'));
        } else {
            $this->entityManager->beginTransaction();
            try {
                $orderGoods = $this->entityManager->getRepository(OrderGoodsReturn::class)->findBy(['orderReturnId' => $returnId]);
                if($orderGoods) {
                    foreach ($orderGoods as $goodsValue) {
                        $this->orderGoodsManager->updateOrderGoodsBuyNumAndAmountAdd(
                            [
                                'pOrderId'  => $returnInfo->getPOrderId(),
                                'pGoodsId'  => $goodsValue->getPGoodsId(),
                                'goodsReturnNum' => $goodsValue->getGoodsReturnNum(),
                                'goodsReturnAmount' => $goodsValue->getGoodsReturnAmount()
                            ]
                        );
                        $this->orderGoodsReturnManager->deleteOrderGoodsReturn($goodsValue);
                    }

                    $orderInfo = $this->entityManager->getRepository(Order::class)->findOneByPOrderId($returnInfo->getPOrderId());
                    $this->orderManager->updateOrderAmount(
                        [
                            'pOrderGoodsAmount' => $orderInfo->getPOrderGoodsAmount() + $returnInfo->getPOrderGoodsReturnAmount(),
                            'pOrderAmount'      => $orderInfo->getPOrderAmount() + $returnInfo->getPOrderReturnAmount()
                        ],
                        $orderInfo);
                    $this->orderReturnManager->deleteOrderReturn($returnInfo);
                    $this->purchaseCommon()->delPurchaseOperLog($returnInfo->getPOrderId(), true);

                    $oneReturnInfo = $this->entityManager->getRepository(OrderReturn::class)->findOneBy(['pOrderId' => $returnInfo->getPOrderId()]);
                    if($oneReturnInfo == null) $this->orderManager->updateOrderReturnState(0, $orderInfo);
                }
                $this->entityManager->commit();

                $message = $returnInfo->getPOrderSn() . $this->translator->translate('取消退货成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('采购退货'));
                $this->flashMessenger()->addSuccessMessage($message);
            } catch (\Exception $e) {
                $this->flashMessenger()->addWarningMessage($this->translator->translate('取消退货失败！'));
                $this->entityManager->rollback();
            }
        }

        return $this->adminCommon()->toReferer();
    }

    /**
     * 退货操作
     * @return array|\Zend\Http\Response
     */
    public function returnOrderAction()
    {
        $orderId = (int) $this->params()->fromRoute('id', -1);

        $orderInfo = $this->entityManager->getRepository(Order::class)->findOneByPOrderId($orderId);
        if($orderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该订单不存在！'));
            return $this->redirect()->toRoute('p-order');
        }
        if($orderInfo->getPOrderState() != 1) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该订单状态不符合退货要求！'));
            return $this->redirect()->toRoute('p-order');
        }

        $form = new OrderGoodsReturnForm($this->entityManager, $orderId);
        $formData = [];
        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();

                $this->entityManager->beginTransaction();
                try {
                    $orderReturn = $this->orderReturnManager->addOrderReturn($data, $orderInfo, $this->adminSession('admin_id'));
                    $returnArray = $this->orderGoodsReturnManager->addOrderGoodsReturn($data, $orderReturn);
                    $this->orderGoodsManager->updateOrderGoodsBuyNumAndAmountSub($returnArray['pGoods']);
                    $this->orderReturnManager->updateOrderReturnAmount($returnArray['goodsReturnAmount'], $returnArray['returnAmount'], $orderReturn);
                    $this->orderManager->updateOrderAmount(
                        [
                            'pOrderGoodsAmount' => $orderInfo->getPOrderGoodsAmount() - $returnArray['goodsReturnAmount'],
                            'pOrderAmount' => $orderInfo->getPOrderAmount() - $returnArray['returnAmount']
                        ],
                        $orderInfo);
                    $this->orderManager->updateOrderReturnState(1, $orderInfo);
                    $this->purchaseCommon()->addPurchaseOperLog(-1, $orderId);

                    $this->entityManager->commit();

                    $message = $orderInfo->getPOrderSn() . $this->translator->translate('退货添加完成！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('采购退货'));
                    $this->flashMessenger()->addSuccessMessage($message);

                    return $this->redirect()->toRoute('order-return');
                } catch (\Exception $exception) {
                    $this->entityManager->rollback();

                    $this->flashMessenger()->addWarningMessage($this->translator->translate('退货添加失败！'));
                    return $this->redirect()->toRoute('p-order');
                }


            } else $formData = $data;
        }

        $orderGoods = $this->entityManager->getRepository(OrderGoods::class)->findBy(['pOrderId' => $orderId], ['pGoodsId' => 'ASC']);

        return ['form' => $form, 'formData' => $formData, 'orderInfo' => $orderInfo, 'orderGoods' => $orderGoods];
    }
}