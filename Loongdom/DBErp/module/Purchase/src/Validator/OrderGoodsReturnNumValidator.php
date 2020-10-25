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

class OrderGoodsReturnNumValidator extends AbstractValidator
{
    const NOT_SCALAR    = 'notScalar';
    const NUM_FALSE     = 'numFalse';
    const NUM_NOT_MINUS = 'numNotMinus';
    const NOT_NUMBER    = 'notNumber';
    const NOT_ZERO      = 'notZero';

    protected $options = [
        'entityManager'
    ];

    protected $messageTemplates = [
        self::NOT_SCALAR    => "这不是一个标准输入值",
        self::NUM_FALSE     => "退货数量超过标准数量",
        self::NUM_NOT_MINUS => "退货数量不能为负数",
        self::NOT_NUMBER    => "退货数量不是一个标准的数字",
        self::NOT_ZERO      => "退货数量不能为0"
    ];

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->options['entityManager'] = $options['entityManager'];
        }

        parent::__construct($options);
    }

    public function isValid($value, $context=null)
    {
        $isValid = true;

        if(empty($context['pGoodsId'])) return $isValid;

        if(!is_array($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $entityManager  = $this->options['entityManager'];

        foreach ($value as $pGoodsId => $num) {
            if(!in_array($pGoodsId, $context['pGoodsId'])) continue;

            if(!is_numeric($num) || !is_numeric($pGoodsId)) {
                $this->error(self::NOT_NUMBER);
                return false;
            }
            if($num == 0) {
                $this->error(self::NOT_ZERO);
                return false;
            }
            if($num < 0) {
                $this->error(self::NUM_NOT_MINUS);
                return false;
            }
            $orderGoodsInfo = $entityManager->getRepository(OrderGoods::class)->findOneByPGoodsId($pGoodsId);
            if($num > $orderGoodsInfo->getPGoodsBuyNum()) {
                $this->error(self::NUM_FALSE);
                return false;
            }
        }

        return $isValid;
    }
}