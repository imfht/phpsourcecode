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

use Sales\Entity\SalesOrder;
use Zend\Validator\AbstractValidator;

class SalesOrderCodeValidator extends AbstractValidator
{
    const NOT_SCALAR        = 'notScalar';
    const SALES_ORDER_CODE_EXISTS = 'salesOrderCodeExists';

    protected $messageTemplates = [
        self::NOT_SCALAR        => "这不是一个标准输入值",
        self::SALES_ORDER_CODE_EXISTS => "该销售订单号已经存在"
    ];

    private $entityManager;
    private $salesOrder;

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->entityManager= $options['entityManager'];
            if(isset($options['salesOrder']))       $this->salesOrder   = $options['salesOrder'];
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $salesOrderInfo = $this->entityManager->getRepository(SalesOrder::class)->findOneBySalesOrderSn($value);

        if($this->salesOrder == null) {
            $isValid = ($salesOrderInfo==null);
        } else {
            if($this->salesOrder->getSalesOrderSn() != $value && $salesOrderInfo != null)
                $isValid = false;
            else
                $isValid = true;
        }

        if(!$isValid) $this->error(self::SALES_ORDER_CODE_EXISTS);

        return $isValid;
    }
}