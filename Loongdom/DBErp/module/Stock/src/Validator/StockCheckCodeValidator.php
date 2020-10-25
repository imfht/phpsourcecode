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

namespace Stock\Validator;

use Stock\Entity\StockCheck;
use Zend\Validator\AbstractValidator;

class StockCheckCodeValidator extends AbstractValidator
{
    const NOT_SCALAR                = 'notScalar';
    const STOCK_CHECK_CODE_EXISTS   = 'stockCheckCodeExists';

    protected $messageTemplates = [
        self::NOT_SCALAR                => "这不是一个标准输入值",
        self::STOCK_CHECK_CODE_EXISTS   => "该盘点单号已经存在"
    ];

    private $entityManager;
    private $stockCheck;

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->entityManager    = $options['entityManager'];
            if(isset($options['stockCheck']))       $this->stockCheck       = $options['stockCheck'];
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $stockCheckInfo = $this->entityManager->getRepository(StockCheck::class)->findOneBy(['stockCheckSn' => $value]);

        if($this->stockCheck == null) {
            $isValid = ($stockCheckInfo==null);
        } else {
            if($this->stockCheck->getStockCheckSn() != $value && $stockCheckInfo != null) $isValid = false;
            else $isValid = true;
        }

        if(!$isValid) $this->error(self::STOCK_CHECK_CODE_EXISTS);

        return $isValid;
    }
}