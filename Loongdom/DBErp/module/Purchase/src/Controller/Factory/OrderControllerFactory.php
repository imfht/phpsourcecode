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

namespace Purchase\Controller\Factory;

use Interop\Container\ContainerInterface;
use Purchase\Controller\OrderController;
use Purchase\Service\OrderGoodsManager;
use Purchase\Service\OrderGoodsReturnManager;
use Purchase\Service\OrderManager;
use Purchase\Service\OrderReturnManager;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\Factory\FactoryInterface;

class OrderControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $translator         = $container->get(Translator::class);
        $entityManager      = $container->get('doctrine.entitymanager.orm_default');
        $orderManager       = $container->get(OrderManager::class);
        $orderGoodsManager  = $container->get(OrderGoodsManager::class);
        $orderReturnManager = $container->get(OrderReturnManager::class);
        $orderGoodsReturnManager  = $container->get(OrderGoodsReturnManager::class);

        return new OrderController(
            $translator,
            $entityManager,
            $orderManager,
            $orderGoodsManager,
            $orderReturnManager,
            $orderGoodsReturnManager
        );
    }
}