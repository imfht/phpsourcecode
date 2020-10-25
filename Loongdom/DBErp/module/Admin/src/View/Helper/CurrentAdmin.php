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

namespace Admin\View\Helper;

use Zend\Session\Container;
use Zend\View\Helper\AbstractHelper;

class CurrentAdmin extends AbstractHelper
{
    private $authService;

    private $sessionManager;

    private $admin = null;

    public function __construct($authService, $sessionManager)
    {
        $this->authService      = $authService;
        $this->sessionManager   = $sessionManager;
    }

    public function __invoke($adminField)
    {
        if($this->admin !== null && isset($this->admin->$adminField)) {
            return $this->admin->$adminField;
        }
        if($this->authService->hasIdentity()) {
            $this->admin = new Container('admin', $this->sessionManager);

            if(!empty($this->admin->$adminField)) {
                return $this->admin->$adminField;
            }
        }
        return null;
    }
}