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

namespace Customer\Validator;

use Customer\Entity\Supplier;
use Zend\Validator\AbstractValidator;

class SupplierCodeValidator extends AbstractValidator
{
    const NOT_SCALAR        = 'notScalar';
    const SUPPLIER_CODE_EXISTS = 'supplierCategoryCodeExists';

    protected $messageTemplates = [
        self::NOT_SCALAR        => "这不是一个标准输入值",
        self::SUPPLIER_CODE_EXISTS => "供应商编码已经存在"
    ];

    private $entityManager;
    private $supplier;

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->entityManager= $options['entityManager'];
            if(isset($options['supplier']))         $this->supplier     = $options['supplier'];
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $supplierInfo = $this->entityManager->getRepository(Supplier::class)->findOneBySupplierCode($value);

        if($this->supplier == null) {
            $isValid = ($supplierInfo == null);
        } else {
            if($this->supplier->getSupplierCode() != $value && $supplierInfo != null)
                $isValid = false;
            else
                $isValid = true;
        }

        if(!$isValid) $this->error(self::SUPPLIER_CODE_EXISTS);

        return $isValid;
    }
}