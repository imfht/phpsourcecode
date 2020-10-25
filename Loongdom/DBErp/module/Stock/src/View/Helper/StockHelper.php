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

namespace Stock\View\Helper;

use Admin\Data\Common;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\I18n\Translator;
use Zend\View\Helper\AbstractHelper;

class StockHelper extends AbstractHelper
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

    /**
     * 库存盘点状态
     * @param $state
     * @param int $style
     * @return mixed
     */
    public function stockCheckState($state, $style = 1)
    {
        $checkState = Common::StockCheckState($this->translator, $style);

        return $checkState[$state];
    }
}