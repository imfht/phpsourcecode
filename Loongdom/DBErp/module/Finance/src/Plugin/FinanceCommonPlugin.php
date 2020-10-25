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

namespace Finance\Plugin;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\I18n\Translator;

class FinanceCommonPlugin extends AbstractPlugin
{
    private $entityManager;
    private $translator;

    public function __construct(
        EntityManager   $entityManager,
        Translator      $translator
    )
    {
        $this->entityManager    = $entityManager;
        $this->translator       = $translator;
    }
}