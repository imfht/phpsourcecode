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

namespace Store\Validator;

use Store\Entity\Warehouse;
use Zend\Validator\AbstractValidator;

class WarehouseCodeValidator extends AbstractValidator
{
    const NOT_SCALAR            = 'notScalar';
    const WAREHOUSE_CODE_EXISTS = 'warehouseCodeExists';

    protected $messageTemplates = [
        self::NOT_SCALAR               => "这不是一个标准输入值",
        self::WAREHOUSE_CODE_EXISTS    => "该仓库编码已经存在"
    ];

    private $entityManager;
    private $warehouse;

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->entityManager= $options['entityManager'];
            if(isset($options['warehouse']))         $this->warehouse   = $options['warehouse'];
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $warehouseInfo = $this->entityManager->getRepository(Warehouse::class)->findOneByWarehouseSn($value);

        if($this->warehouse == null) {
            $isValid = ($warehouseInfo==null);
        } else {
            if($this->warehouse->getWarehouseSn() != $value && $warehouseInfo != null)
                $isValid = false;
            else
                $isValid = true;
        }

        if(!$isValid) $this->error(self::WAREHOUSE_CODE_EXISTS);

        return $isValid;
    }
}