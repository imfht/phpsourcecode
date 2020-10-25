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

namespace Shop\View;

use Admin\Data\Common;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\I18n\Translator;
use Zend\View\Helper\AbstractHelper;

class ShopHelper extends AbstractHelper
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
     * 商城订单状态
     * @param $state
     * @param int $style
     * @return mixed
     */
    public function shopOrderState($state, $style = 1)
    {
        $shopOrderState = Common::shopOrderState($this->translator, $style);

        return $shopOrderState[$state];
    }

    /**
     * 商城订单商品与erp商品匹配状态
     * @param $state
     * @param int $style
     * @return mixed
     */
    public function shopDistributionState($state, $style = 1)
    {
        $shopDistributionState = Common::distributionState($this->translator, $style);

        return $shopDistributionState[$state];
    }
}