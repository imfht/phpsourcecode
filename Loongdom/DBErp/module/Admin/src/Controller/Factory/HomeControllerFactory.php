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

namespace Admin\Controller\Factory;

use Admin\Controller\HomeController;
use Admin\Report\HomeReport;
use Interop\Container\ContainerInterface;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\Factory\FactoryInterface;

class HomeControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager          = $container->get('doctrine.entitymanager.orm_default');
        $i18nSessionContainer   = $container->get('I18nSessionContainer');
        $translator             = $container->get(Translator::class);
        $homeReport             = $container->get(HomeReport::class);

        return new HomeController($translator, $entityManager, $homeReport, $i18nSessionContainer);
    }
}