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

use Purchase\Entity\WarehouseOrder;
use Zend\Validator\AbstractValidator;

class WarehouseOrderCodeValidator extends AbstractValidator
{
    const NOT_SCALAR                  = 'notScalar';
    const WAREHOUSE_ORDER_CODE_EXISTS = 'warehouseOrderCodeExists';

    protected $messageTemplates = [
        self::NOT_SCALAR                    => "这不是一个标准输入值",
        self::WAREHOUSE_ORDER_CODE_EXISTS   => "该入库单号已经存在"
    ];

    private $entityManager;
    private $warehouseOrder;

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->entityManager    = $options['entityManager'];
            if(isset($options['warehouseOrder']))   $this->warehouseOrder   = $options['warehouseOrder'];
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $warehouseOrderInfo = $this->entityManager->getRepository(WarehouseOrder::class)->findOneByWarehouseOrderSn($value);

        if($this->warehouseOrder == null) {
            $isValid = ($warehouseOrderInfo==null);
        } else {
            if($this->warehouseOrder->getWarehouseOrderSn() != $value && $warehouseOrderInfo != null)
                $isValid = false;
            else
                $isValid = true;
        }

        if(!$isValid) $this->error(self::WAREHOUSE_ORDER_CODE_EXISTS);

        return $isValid;
    }
}