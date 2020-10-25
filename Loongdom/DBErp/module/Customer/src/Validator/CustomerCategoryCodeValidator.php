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

use Customer\Entity\CustomerCategory;
use Zend\Validator\AbstractValidator;

class CustomerCategoryCodeValidator extends AbstractValidator
{
    const NOT_SCALAR        = 'notScalar';
    const CUSTOMER_CATEGORY_CODE_EXISTS = 'customerCategoryCodeExists';

    protected $messageTemplates = [
        self::NOT_SCALAR        => "这不是一个标准输入值",
        self::CUSTOMER_CATEGORY_CODE_EXISTS => "客户分类编码已经存在"
    ];

    private $entityManager;
    private $customerCategory;

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->entityManager    = $options['entityManager'];
            if(isset($options['customerCategory'])) $this->customerCategory = $options['customerCategory'];
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $customerCategoryInfo = $this->entityManager->getRepository(CustomerCategory::class)->findOneByCustomerCategoryCode($value);

        if($this->customerCategory == null) {
            $isValid = ($customerCategoryInfo == null);
        } else {
            if($this->customerCategory->getCustomerCategoryCode() != $value && $customerCategoryInfo != null)
                $isValid = false;
            else
                $isValid = true;
        }

        if(!$isValid) $this->error(self::CUSTOMER_CATEGORY_CODE_EXISTS);

        return $isValid;
    }
}