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
use Shop\Entity\ShopOrderGoods;
use Shop\Form\SearchShopOrderGoodsForm;
use Shop\Service\ShopOrderGoodsManager;
use Shop\Service\ShopOrderManager;
use Store\Entity\Goods;
use Store\Entity\WarehouseGoods;
use Store\Service\GoodsManager;
use Store\Service\WarehouseGoodsManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class OrderGoodsController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $shopOrderManager;
    private $shopOrderGoodsManager;
    private $warehouseGoodsManager;
    private $goodsManager;

    public function __construct(
        Translator $translator,
        EntityManager $entityManager,
        ShopOrderManager $shopOrderManager,
        ShopOrderGoodsManager $shopOrderGoodsManager,
        WarehouseGoodsManager $warehouseGoodsManager,
        GoodsManager $goodsManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->shopOrderManager = $shopOrderManager;
        $this->shopOrderGoodsManager = $shopOrderGoodsManager;
        $this->warehouseGoodsManager = $warehouseGoodsManager;
        $this->goodsManager     = $goodsManager;
    }

    /**
     * 订单商品列表
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);

        $search= [];
        $searchForm = new SearchShopOrderGoodsForm();
        $searchForm->get('order_state')->setValueOptions(Common::shopOrderState($this->translator));
        $searchForm->get('app_id')->setValueOptions($this->adminCommon()->appShopOptions());
        $searchForm->get('warehouse_id')->setValueOptions($this->storeCommon()->warehouseListOptions());
        $searchForm->get('distribution_state')->setValueOptions(Common::distributionState($this->translator));
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }

        $query = $this->entityManager->getRepository(ShopOrderGoods::class)->findShopOrderGoodsAll($search);
        $shopOrderGoodsList = $this->adminCommon()->erpPaginator($query, $page);

        return ['orderGoodsList' => $shopOrderGoodsList, 'searchForm' => $searchForm];
    }

    /**
     * 订单商品与erp商品匹配处理
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function distributionGoodsAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $orderGoodsId   = (int) $this->params()->fromRoute('id', -1);
        $orderGoodsInfo = $this->entityManager->getRepository(ShopOrderGoods::class)->findOneBy(['orderGoodsId' => $orderGoodsId]);
        if($orderGoodsInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该订单商品不存在！'));
            return $this->adminCommon()->toReferer();
        }
        if($orderGoodsInfo->getDistributionState() != 3) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('状态不符合，无法匹配！'));
            return $this->adminCommon()->toReferer();
        }

        $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsNumber' => $orderGoodsInfo->getGoodsSn()]);
        if($goodsInfo) {
            $this->shopOrderGoodsManager->updateShopOrderGoodsState(4, $orderGoodsInfo);
            $this->flashMessenger()->addSuccessMessage($this->translator->translate('该订单商品匹配完成！'));
        } else $this->flashMessenger()->addWarningMessage($this->translator->translate('订单商品无法与系统中的商品匹配！'));

        return $this->adminCommon()->toReferer();
    }

    /**
     * 完成补货
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function finishDistributionAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $orderGoodsId   = (int) $this->params()->fromRoute('id', -1);
        $orderGoodsInfo = $this->entityManager->getRepository(ShopOrderGoods::class)->findOneBy(['orderGoodsId' => $orderGoodsId]);
        if($orderGoodsInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该订单商品不存在！'));
            return $this->adminCommon()->toReferer();
        }
        if($orderGoodsInfo->getDistributionState() != -1) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('状态不符合，无法补货！'));
            return $this->adminCommon()->toReferer();
        }

        $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsNumber' => $orderGoodsInfo->getGoodsSn()]);
        if($goodsInfo) {
            $warehouse = $this->entityManager->getRepository(WarehouseGoods::class)->findWarehouseStockGoods($goodsInfo->getGoodsId(), $orderGoodsInfo->getBuyNum());
            if($warehouse) {
                foreach ($warehouse as $warehouseGoods) {
                    $this->warehouseGoodsManager->updateWarehouseGoodsStock(($warehouseGoods->getWarehouseGoodsStock() - $orderGoodsInfo->getBuyNum()), $warehouseGoods);
                    $this->goodsManager->updateGoodsStock(($goodsInfo->getGoodsStock() - $orderGoodsInfo->getBuyNum()), $goodsInfo);

                    $state = [40 => 6, 60 => 12];
                    $this->shopOrderGoodsManager->addShopOrderGoodsWarehouseAndState($warehouseGoods->getWarehouseId(), $warehouseGoods->getOneWarehouse()->getWarehouseName(), $state[$orderGoodsInfo->getOneShopOrder()->getShopOrderState()], $orderGoodsInfo);

                    $this->flashMessenger()->addSuccessMessage($this->translator->translate('补货完成！'));
                    break;
                }
            } else $this->flashMessenger()->addWarningMessage($this->translator->translate('库存不足，无法补货！'));
        }

        return $this->adminCommon()->toReferer();
    }
}