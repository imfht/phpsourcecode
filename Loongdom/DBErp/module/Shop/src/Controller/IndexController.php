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

namespace Shop\Controller;

use Admin\Data\Common;
use Doctrine\ORM\EntityManager;
use Shop\Entity\ShopOrder;
use Shop\Entity\ShopOrderDeliveryAddress;
use Shop\Entity\ShopOrderGoods;
use Shop\Form\SearchShopOrderForm;
use Shop\Service\ShopOrderDeliveryAddressManager;
use Shop\Service\ShopOrderGoodsManager;
use Shop\Service\ShopOrderManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class IndexController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $shopOrderManager;
    private $shopOrderGoodsManager;
    private $shopOrderDeliveryAddressManager;

    public function __construct(
        Translator $translator,
        EntityManager $entityManager,
        ShopOrderManager $shopOrderManager,
        ShopOrderGoodsManager $shopOrderGoodsManager,
        ShopOrderDeliveryAddressManager $shopOrderDeliveryAddressManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->shopOrderManager = $shopOrderManager;
        $this->shopOrderGoodsManager = $shopOrderGoodsManager;
        $this->shopOrderDeliveryAddressManager = $shopOrderDeliveryAddressManager;
    }

    /**
     * 商城订单
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);

        $search= [];
        $searchForm = new SearchShopOrderForm();
        $searchForm->get('order_state')->setValueOptions(Common::shopOrderState($this->translator));
        $searchForm->get('app_id')->setValueOptions($this->adminCommon()->appShopOptions());
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }

        $query = $this->entityManager->getRepository(ShopOrder::class)->findShopOrderAll($search);
        $shopOrderList = $this->adminCommon()->erpPaginator($query, $page);

        return ['orderList' => $shopOrderList, 'searchForm' => $searchForm];
    }

    /**
     * 订单详情
     * @return array
     */
    public function viewAction()
    {
        $shopOrderId = (int) $this->params()->fromRoute('id', -1);
        $shopOrderInfo = $this->entityManager->getRepository(ShopOrder::class)->findOneBy(['shopOrderId' => $shopOrderId]);
        if($shopOrderInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该订单不存在！'));
            return $this->adminCommon()->toReferer();
        }

        $orderGoods     = $this->entityManager->getRepository(ShopOrderGoods::class)->findBy(['shopOrderId' => $shopOrderId]);
        $deliveryInfo   = $this->entityManager->getRepository(ShopOrderDeliveryAddress::class)->findOneBy(['shopOrderId' => $shopOrderId]);

        return ['orderInfo' => $shopOrderInfo, 'orderGoods' => $orderGoods, 'deliveryInfo' => $deliveryInfo];
    }

    /**
     * 订单删除
     * @return mixed
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $shopOrderId = (int) $this->params()->fromRoute('id', -1);
        $shopOrderInfo = $this->entityManager->getRepository(ShopOrder::class)->findOneBy(['shopOrderId' => $shopOrderId]);
        if($shopOrderInfo == null || $shopOrderInfo->getShopOrderState() != 0) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该订单不存在或该订单状态不允许删除！'));
            return $this->adminCommon()->toReferer();
        }

        $this->entityManager->beginTransaction();
        try {
            $this->shopOrderManager->deleteShopOrder($shopOrderInfo);
            $this->shopOrderGoodsManager->deleteShopOrderGoods($shopOrderId);
            $this->shopOrderDeliveryAddressManager->deleteShopOrderDeliveryAddress($shopOrderId);

            $this->entityManager->commit();

            $message = sprintf($this->translator->translate('商城订单 %s 删除成功！'), $shopOrderInfo->getShopOrderSn());
            $this->adminCommon()->addOperLog($message, $this->translator->translate('商城订单'));
            $this->flashMessenger()->addSuccessMessage($message);
        } catch (\Exception $e) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('订单删除失败！'));
            $this->entityManager->rollback();
        }
        return $this->adminCommon()->toReferer();
    }
}