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

namespace Admin\View\Helper\Factory;

use Admin\View\Helper\CurrentAdmin;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;

class CurrentAdminFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService    = $container->get(AuthenticationService::class);
        $sessionManager = $container->get(SessionManager::class);
        return new CurrentAdmin($authService, $sessionManager);
    }
}