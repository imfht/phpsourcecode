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

namespace Admin\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;
use Zend\Session\SessionManager;

class AdminSessionPlugin extends AbstractPlugin
{
    private $adminSession;

    public function __construct(
        SessionManager $sessionManager
    )
    {
        $this->adminSession = new Container('admin', $sessionManager);
    }

    public function __invoke(string $name)
    {
        return $this->adminSession->$name;
    }

}