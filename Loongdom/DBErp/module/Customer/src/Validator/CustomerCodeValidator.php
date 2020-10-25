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

use Customer\Entity\Customer;
use Zend\Validator\AbstractValidator;

class CustomerCodeValidator extends AbstractValidator
{
    const NOT_SCALAR        = 'notScalar';
    const CUSTOMER_CODE_EXISTS = 'customerCodeExists';

    protected $messageTemplates = [
        self::NOT_SCALAR        => "这不是一个标准输入值",
        self::CUSTOMER_CODE_EXISTS => "客户编码已经存在"
    ];

    private $entityManager;
    private $customer;

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->entityManager= $options['entityManager'];
            if(isset($options['customer']))         $this->customer     = $options['customer'];
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $customerInfo = $this->entityManager->getRepository(Customer::class)->findOneByCustomerCode($value);

        if($this->customer == null) {
            $isValid = ($customerInfo == null);
        } else {
            if($this->customer->getCustomerCode() != $value && $customerInfo != null)
                $isValid = false;
            else
                $isValid = true;
        }

        if(!$isValid) $this->error(self::CUSTOMER_CODE_EXISTS);

        return $isValid;
    }
}