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

namespace Sales\Controller\Factory;

use Interop\Container\ContainerInterface;
use Sales\Controller\SalesOrderController;
use Sales\Service\SalesGoodsPriceLogManager;
use Sales\Service\SalesOrderGoodsManager;
use Sales\Service\SalesOrderManager;
use Sales\Service\SalesSendOrderManager;
use Sales\Service\SalesSendWarehouseGoodsManager;
use Store\Service\GoodsManager;
use Store\Service\WarehouseGoodsManager;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\Factory\FactoryInterface;

class SalesOrderControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $translator                     = $container->get(Translator::class);
        $entityManager                  = $container->get('doctrine.entitymanager.orm_default');
        $salesOrderManager              = $container->get(SalesOrderManager::class);
        $salesOrderGoodsManager         = $container->get(SalesOrderGoodsManager::class);
        $salesSendOrderManager          = $container->get(SalesSendOrderManager::class);
        $salesSendWarehouseGoodsManager = $container->get(SalesSendWarehouseGoodsManager::class);
        $warehouseGoodsManager          = $container->get(WarehouseGoodsManager::class);
        $goodsManager                   = $container->get(GoodsManager::class);
        $salesGoodsPriceLogManager      = $container->get(SalesGoodsPriceLogManager::class);

        return new SalesOrderController(
            $translator,
            $entityManager,
            $salesOrderManager,
            $salesOrderGoodsManager,
            $salesSendOrderManager,
            $salesSendWarehouseGoodsManager,
            $warehouseGoodsManager,
            $goodsManager,
            $salesGoodsPriceLogManager
        );
    }
}