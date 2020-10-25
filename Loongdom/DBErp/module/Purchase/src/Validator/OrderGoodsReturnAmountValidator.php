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

class OrderGoodsReturnAmountValidator extends AbstractValidator
{
    const NOT_SCALAR    = 'notScalar';
    const AMOUNT_FALSE  = 'amountFalse';
    const AMOUNT_NOT_MINUS = 'amountNotMinus';
    const NOT_NUMBER    = 'notNumber';

    protected $options = [
        'entityManager'
    ];

    protected $messageTemplates = [
        self::NOT_SCALAR    => "这不是一个标准输入值",
        self::AMOUNT_FALSE  => "商品退货金额超出标准金额",
        self::AMOUNT_NOT_MINUS => "退货金额不能为负数",
        self::NOT_NUMBER    => "退货金额不是一个标准的数值"
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

        foreach ($value as $pGoodsId => $amount) {
            if(!in_array($pGoodsId, $context['pGoodsId'])) continue;

            if(!is_numeric($pGoodsId) || !is_numeric($amount)) {
                $this->error(self::NOT_NUMBER);
                return false;
            }
            if($amount == 0) continue;
            if($amount < 0) {//金额不能为负数
                $this->error(self::AMOUNT_NOT_MINUS);
                return false;
            }
            $orderGoodsInfo = $entityManager->getRepository(OrderGoods::class)->findOneByPGoodsId($pGoodsId);
            if($amount > $orderGoodsInfo->getPGoodsAmount()) {//金额不能超过采购的商品金额
                $this->error(self::AMOUNT_FALSE);
                return false;
            }
        }

        return $isValid;
    }
}