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

namespace Store\Event\Factory;

use Interop\Container\ContainerInterface;
use Store\Event\Listener;
use Store\Service\GoodsManager;
use Store\Service\WarehouseGoodsManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class ListenerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager  = $container->get('doctrine.entitymanager.orm_default');
        $goodsManager   = $container->get(GoodsManager::class);
        $warehouseGoodsManager = $container->get(WarehouseGoodsManager::class);

        return new Listener($entityManager, $goodsManager, $warehouseGoodsManager);
    }
}