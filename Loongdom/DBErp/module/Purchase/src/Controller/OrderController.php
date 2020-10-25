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
use Purchase\Entity\PurchaseOperLog;
use Purchase\Form\OrderForm;
use Purchase\Form\OrderGoodsForm;
use Purchase\Form\SearchOrderForm;
use Purchase\Service\OrderGoodsManager;
use Purchase\Service\OrderGoodsReturnManager;
use Purchase\Service\OrderManager;
use Purchase\Service\OrderReturnManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;

/**
 * 采购订单
 * Class OrderController
 * @package Purchase\Controller
 */
class OrderController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $orderManager;
    private $orderGoodsManager;
    private $orderReturnManager;
    private $orderGoodsReturnManager;

    public function __construct(
        Translator          $translator,
        EntityManager       $entityManager,
        OrderManager        $orderManager,
        OrderGoodsManager   $orderGoodsManager,
        OrderReturnManager  $orderReturnManager,
        OrderGoodsReturnManager $orderGoodsReturnManager
    )
    {
        $this->translator           = $translator;
        $this->entityManager        = $entityManager;
        $this->orderManager         = $orderManager;
        $this->orderGoodsManager    = $orderGoodsManager;
        $this->orderReturnManager   = $orderReturnManager;
        $this->orderGoodsReturnManager = $orderGoodsReturnManager;
    }

    /**
     * 采购订单列表
     * @return array
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $array= [];

        $search = [];
        $searchForm = new SearchOrderForm();
        $searchForm->get('supplier_id')->setValueOptions($this->customerCommon()->supplierListOptions());
        $searchForm->get('payment_code')->setValueOptions(Common::payment($this->translator));
        $searchForm->get('return_state')->setValueOptions(Common::existReturn($this->translator));
        $pOrderStateArray = Common::purchaseOrderState($this->translator);
        unset($pOrderStateArray[-5], $pOrderStateArray[-1]);
        $searchForm->get('p_order_state')->setValueOptions($pOrderStateArray);
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }
        $array['searchForm'] = $searchForm;

        $query = $this->entityManager->getRepository(Order::class)->findAllOrder($search);
        $array['orderList'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }


    /**
     * 添加采购订单
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $goodsForm  = new OrderGoodsForm($this->entityManager);
        $form       = new OrderForm($this->entityManager);
        $form->get('paymentCode')->setValueOptions(Common::payment($this->translator));
        $form->get('supplierId')->setValueOptions($this->customerCommon()->supplierListOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            $goodsForm->setData($data);
            if($form->isValid() && $goodsForm->isValid()) {
                $data = $form->getData();
                $goodsData = $goodsForm->getData();

                $this->entityManager->beginTransaction();
                try {
                    $orderInfo = $this->orderManager->addOrder($data, $goodsData, $this->adminSession('admin_id'));
                    $this->orderGoodsManager->addOrderGoods($goodsData, $orderInfo->getPOrderId());
                    $this->purchaseCommon()->addPurchaseOperLog($orderInfo->getPOrderState(), $orderInfo->getPOrderId());

                    $this->entityManager->commit();

                    $message = $orderInfo->getPOrderSn() . $this->translator->translate('采购订单添加成功！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('采购订单'));
                    $this->flashMessenger()->addSuccessMessage($message);
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('采购订单添加失败！'));
                }
                return $this->redirect()->toRoute('p-order');
            }
        } else $form->get('pOrderSn')->setValue($this->purchaseCommon()->createOrderSn());

        return ['form' => $form, 'goodsForm' => $goodsForm];
    }

    /**
     * 编辑采购订单
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $orderId = (int) $this->params()->fromRoute('id', -1);

        $orderInfo = $this->entityManager->getRepository(Order::class)->findOneByPOrderId($orderId);
        if($orderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该订单不存在！'));
            return $this->redirect()->toRoute('p-order');
        }
        if($orderInfo->getPOrderState() != 0) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该订单所属状态不能被编辑！'));
            return $this->redirect()->toRoute('p-order');
        }

        $goodsForm  = new OrderGoodsForm($this->entityManager);
        $form       = new OrderForm($this->entityManager, $orderInfo);

        $form->get('paymentCode')->setValueOptions(Common::payment($this->translator));
        $form->get('supplierId')->setValueOptions($this->customerCommon()->supplierListOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            $goodsForm->setData($data);
            if($form->isValid() && $goodsForm->isValid()) {
                $data = $form->getData();
                $goodsData = $goodsForm->getData();

                $this->entityManager->beginTransaction();
                try {
                    $this->orderManager->updateOrder($data, $goodsData, $orderInfo);
                    $this->orderGoodsManager->editOrderGoods($goodsData, $orderId);

                    $this->entityManager->commit();

                    $message = $orderInfo->getPOrderSn() . $this->translator->translate('采购订单编辑成功！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('采购订单'));
                    $this->flashMessenger()->addSuccessMessage($message);
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('采购订单编辑失败！'));
                }
                return $this->redirect()->toRoute('p-order');
            }
        } else $form->setData($orderInfo->valuesArray());

        $orderGoods = $this->entityManager->getRepository(OrderGoods::class)->findBy(['pOrderId' => $orderId], ['pGoodsId' => 'ASC']);

        return ['form' => $form, 'goodsForm' =>$goodsForm, 'orderInfo' => $orderInfo, 'orderGoods' => $orderGoods];
    }

    /**
     * 查看采购订单
     * @return array|\Zend\Http\Response
     */
    public function viewAction()
    {
        $orderId = (int) $this->params()->fromRoute('id', -1);

        $orderInfo = $this->entityManager->getRepository(Order::class)->findOneByPOrderId($orderId);
        if($orderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该订单不存在！'));
            return $this->redirect()->toRoute('p-order');
        }

        $orderGoods = $this->entityManager->getRepository(OrderGoods::class)->findBy(['pOrderId' => $orderId], ['pGoodsId' => 'ASC']);

        $purchaseOperLog = $this->entityManager->getRepository(PurchaseOperLog::class)->findBy(['pOrderId' => $orderId]);

        return ['orderInfo' => $orderInfo, 'orderGoods' => $orderGoods, 'purchaseOperLog' => $purchaseOperLog];
    }

    /**
     * 删除订单
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $orderId = (int) $this->params()->fromRoute('id', -1);
        $orderInfo = $this->entityManager->getRepository(Order::class)->findOneByPOrderId($orderId);
        if($orderInfo && $orderInfo->getPOrderState() == 0) {
            $this->entityManager->beginTransaction();
            try {
                $this->orderManager->deleteOrder($orderInfo);
                $this->orderGoodsManager->deleteMoreOrderIdGoods($orderId);
                $this->purchaseCommon()->delPurchaseOperLog($orderId);

                $this->entityManager->commit();

                $message = $orderInfo->getPOrderSn() . $this->translator->translate('采购订单删除成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('采购订单'));
                $this->flashMessenger()->addSuccessMessage($message);
            } catch (\Exception $e) {
                $this->entityManager->rollback();
                $this->flashMessenger()->addWarningMessage($this->translator->translate('采购订单删除失败！'));
            }
        }

        return $this->adminCommon()->toReferer();
    }

    /**
     * ajax删除订单商品
     * @return JsonModel
     */
    public function delOrderGoodsAction()
    {
        $array = ['state' => 'false'];

        $goodsId = (int) $this->request->getPost('goodsId', 0);
        $orderId = (int) $this->request->getPost('orderId', 0);
        if($orderId > 0 && $goodsId > 0) {
            $orderInfo = $this->entityManager->getRepository(Order::class)->findOneBy(['pOrderId' => $orderId, 'pOrderState' => 0]);
            if($orderInfo) {
                $orderGoodsInfo = $this->entityManager->getRepository(OrderGoods::class)->findOneBy(['pOrderId' => $orderId, 'goodsId' => $goodsId]);
                if($orderGoodsInfo) {
                    $orderUpdate = [
                        'pOrderGoodsAmount' => $orderInfo->getPOrderGoodsAmount() - $orderGoodsInfo->getPGoodsPrice() * $orderGoodsInfo->getPGoodsBuyNum(),
                        'pOrderTaxAmount'   => $orderInfo->getPOrderTaxAmount() - $orderGoodsInfo->getPGoodsTax(),
                        'pOrderAmount'      => $orderInfo->getPOrderAmount() - $orderGoodsInfo->getPGoodsAmount()
                        ];
                    $this->entityManager->beginTransaction();
                    try {
                        $this->orderManager->updateOrderAmount($orderUpdate, $orderInfo);
                        $this->orderGoodsManager->deleteOrderGoods($orderGoodsInfo);
                        $this->entityManager->commit();

                        $this->adminCommon()->addOperLog($orderInfo->getPOrderSn() . $this->translator->translate('删除商品') . '：' . $orderGoodsInfo->getGoodsName(), $this->translator->translate('采购订单'));

                        $array['state'] = 'ok';
                    } catch (\Exception $e) {
                        $this->entityManager->rollback();
                    }
                }
            }
        }

        return new JsonModel($array);
    }

    /**
     * 审核采购单
     * @return \Zend\Http\Response
     */
    public function authPassOrderAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $orderId = (int) $this->params()->fromRoute('id', -1);
        $orderInfo = $this->entityManager->getRepository(Order::class)->findOneBy(['pOrderId' => $orderId, 'pOrderState' => 0]);
        if($orderInfo == null) $this->flashMessenger()->addWarningMessage($this->translator->translate('审核采购单失败！'));
        else {
            $this->orderManager->updateOrderState(['pOrderState' => 1], $orderInfo);
            $this->purchaseCommon()->addPurchaseOperLog(1, $orderId);

            $message = $orderInfo->getPOrderSn() . $this->translator->translate('采购单审核完成！');
            $this->adminCommon()->addOperLog($message, $this->translator->translate('采购订单'));
            $this->flashMessenger()->addSuccessMessage($message);
        }

        return $this->adminCommon()->toReferer();
    }

    /**
     * 取消审核采购单
     * @return \Zend\Http\Response
     */
    public function cancelOrderAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $orderId = (int) $this->params()->fromRoute('id', -1);
        $orderInfo = $this->entityManager->getRepository(Order::class)->findOneBy(['pOrderId' => $orderId, 'pOrderState' => 1]);
        if($orderInfo == null) $this->flashMessenger()->addWarningMessage($this->translator->translate('取消审核采购单失败！'));
        elseif($orderInfo->getReturnState() == 1) $this->flashMessenger()->addWarningMessage($this->translator->translate('存在采购退货，无法取消审核采购单！'));
        else {
            $this->orderManager->updateOrderState(['pOrderState' => 0], $orderInfo);
            $this->purchaseCommon()->addPurchaseOperLog(0, $orderId);

            $message = $orderInfo->getPOrderSn() . $this->translator->translate('采购单取消审核完成！');
            $this->adminCommon()->addOperLog($message, $this->translator->translate('采购订单'));
            $this->flashMessenger()->addSuccessMessage($message);
        }

        return $this->adminCommon()->toReferer();
    }
}