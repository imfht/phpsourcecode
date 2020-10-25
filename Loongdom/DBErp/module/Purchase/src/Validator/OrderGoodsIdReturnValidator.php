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

namespace Purchase\Validator;

use Purchase\Entity\OrderGoods;
use Zend\Validator\AbstractValidator;

class OrderGoodsIdReturnValidator extends AbstractValidator
{
    const NOT_SCALAR                = 'notScalar';
    const ORDER_GOODS_NOT_EXISTS    = 'orderGoodsNotExists';

    private $entityManager;
    private $pOrderId;

    protected $messageTemplates = [
        self::NOT_SCALAR                => "请选择退货商品",
        self::ORDER_GOODS_NOT_EXISTS    => "有些商品不在该采购单中"
    ];

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager'])) $this->entityManager = $options['entityManager'];
            if(isset($options['pOrderId'])) $this->pOrderId = $options['pOrderId'];
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        $isValid = true;
        if (!is_array($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        foreach ($value as $item) {
            $pOrderGoods = $this->entityManager->getRepository(OrderGoods::class)->findOneBy(['pGoodsId' => $item, 'pOrderId' => $this->pOrderId]);
            if(!$pOrderGoods) {
                $this->error(self::ORDER_GOODS_NOT_EXISTS);
                return false;
            }
        }

        return $isValid;
    }
}