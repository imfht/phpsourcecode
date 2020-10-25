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

use Sales\Entity\SalesSendOrder;
use Zend\Validator\AbstractValidator;

class SendOrderSnValidator extends AbstractValidator
{
    const NOT_SCALAR            = 'notScalar';
    const SEND_ORDER_SN_EXISTS  = 'sendOrderSnExists';

    protected $messageTemplates = [
        self::NOT_SCALAR            => "这不是一个标准输入值",
        self::SEND_ORDER_SN_EXISTS  => "该发货单号已经存在"
    ];

    private $entityManager;

    public function __construct($options = null)
    {
        $this->entityManager= $options['entityManager'];

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $sendOrderInfo = $this->entityManager->getRepository(SalesSendOrder::class)->findOneBySendOrderSn($value);

        if(!$sendOrderInfo) {
            $isValid = true;
        } else {
            if($sendOrderInfo->getSendOrderSn() != $value) $isValid = true;
            else $isValid = false;
        }

        if(!$isValid) $this->error(self::SEND_ORDER_SN_EXISTS);

        return $isValid;
    }
}