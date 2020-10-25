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

namespace Sales\Validator;

use Sales\Entity\SalesOrderGoods;
use Zend\Validator\AbstractValidator;

class SalesOrderGoodsReturnAmountValidator extends AbstractValidator
{
    const NOT_SCALAR        = 'notScalar';
    const AMOUNT_FALSE      = 'amountFalse';
    const AMOUNT_NOT_MINUS  = 'amountNotMinus';
    const NOT_NUMBER        = 'notNumber';

    private $entityManager;

    protected $messageTemplates = [
        self::NOT_SCALAR        => "这不是一个标准输入值",
        self::AMOUNT_FALSE      => "商品退货金额超出标准金额",
        self::AMOUNT_NOT_MINUS  => "退货金额不能为负数",
        self::NOT_NUMBER        => "退货金额不是一个标准的数值"
    ];

    public function __construct($options = null)
    {
        $this->entityManager = $options['entityManager'];

        parent::__construct($options);
    }

    public function isValid($value, $context=null)
    {
        $isValid = true;

        if(empty($context['salesGoodsId'])) return $isValid;

        if(!is_array($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        foreach ($value as $salesGoodsId => $amount) {
            if(!in_array($salesGoodsId, $context['salesGoodsId'])) continue;

            if(!is_numeric($salesGoodsId) || !is_numeric($amount)) {
                $this->error(self::NOT_NUMBER);
                return false;
            }
            if($amount == 0) continue;
            if($amount < 0) {//金额不能为负数
                $this->error(self::AMOUNT_NOT_MINUS);
                return false;
            }
            $orderGoodsInfo = $this->entityManager->getRepository(SalesOrderGoods::class)->findOneBySalesGoodsId($salesGoodsId);
            if($amount > $orderGoodsInfo->getSalesGoodsAmount()) {//金额不能超过销售的商品金额
                $this->error(self::AMOUNT_FALSE);
                return false;
            }
        }

        return $isValid;
    }
}