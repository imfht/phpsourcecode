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

namespace Store\Controller\Factory;

use Interop\Container\ContainerInterface;
use Store\Controller\GoodsCategoryController;
use Store\Service\GoodsCategoryManager;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\Factory\FactoryInterface;

class GoodsCategoryControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $translator         = $container->get(Translator::class);
        $entityManager      = $container->get('doctrine.entitymanager.orm_default');
        $goodsCategoryManager = $container->get(GoodsCategoryManager::class);

        return new GoodsCategoryController($translator, $entityManager, $goodsCategoryManager);
    }
}