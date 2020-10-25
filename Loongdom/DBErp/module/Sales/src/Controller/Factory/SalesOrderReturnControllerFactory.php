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
use Sales\Controller\SalesOrderReturnController;
use Sales\Service\SalesOrderGoodsManager;
use Sales\Service\SalesOrderGoodsReturnManager;
use Sales\Service\SalesOrderManager;
use Sales\Service\SalesOrderReturnManager;
use Sales\Service\SalesSendOrderManager;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\Factory\FactoryInterface;

class SalesOrderReturnControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $translator     = $container->get(Translator::class);
        $entityManager  = $container->get('doctrine.entitymanager.orm_default');
        $salesOrderReturnManager        = $container->get(SalesOrderReturnManager::class);
        $salesOrderGoodsReturnManager   = $container->get(SalesOrderGoodsReturnManager::class);
        $salesOrderManager              = $container->get(SalesOrderManager::class);
        $salesOrderGoodsManager         = $container->get(SalesOrderGoodsManager::class);
        $salesSendOrderManager          = $container->get(SalesSendOrderManager::class);

        return new SalesOrderReturnController(
            $translator,
            $entityManager,
            $salesOrderReturnManager,
            $salesOrderGoodsReturnManager,
            $salesOrderManager,
            $salesOrderGoodsManager,
            $salesSendOrderManager
        );
    }
}