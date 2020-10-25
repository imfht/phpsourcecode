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

namespace Shop\Controller\Factory;

use Interop\Container\ContainerInterface;
use Shop\Controller\OrderGoodsController;
use Shop\Service\ShopOrderGoodsManager;
use Shop\Service\ShopOrderManager;
use Store\Service\GoodsManager;
use Store\Service\WarehouseGoodsManager;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\Factory\FactoryInterface;

class OrderGoodsControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager      = $container->get('doctrine.entitymanager.orm_default');
        $translator         = $container->get(Translator::class);
        $shopOrderManager   = $container->get(ShopOrderManager::class);
        $shopOrderGoodsManager = $container->get(ShopOrderGoodsManager::class);
        $warehouseGoodsManager = $container->get(WarehouseGoodsManager::class);
        $goodsManager       = $container->get(GoodsManager::class);

        return new OrderGoodsController(
            $translator,
            $entityManager,
            $shopOrderManager,
            $shopOrderGoodsManager,
            $warehouseGoodsManager,
            $goodsManager
        );
    }
}