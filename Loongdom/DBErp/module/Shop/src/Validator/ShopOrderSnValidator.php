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

namespace Shop\Validator;

use Shop\Entity\ShopOrder;
use Zend\Validator\AbstractValidator;

class ShopOrderSnValidator extends AbstractValidator
{
    const NOT_SCALAR            = 'notScalar';
    const SHOP_ORDER_SN_EXISTS  = 'shopOrderSnExists';

    protected $messageTemplates = [
        self::NOT_SCALAR            => "这不是一个标准输入值",
        self::SHOP_ORDER_SN_EXISTS  => "订单编号已经存在"
    ];

    private $entityManager;
    private $appId;

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->entityManager= $options['entityManager'];
            if(isset($options['appId']))            $this->appId        = $options['appId'];
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $shopOrderInfo = $this->entityManager->getRepository(ShopOrder::class)->findOneBy(['shopOrderSn' => $value, 'appId' => $this->appId]);

        if($shopOrderInfo == null) {
            $isValid = true;
        } else {
            $isValid = false;
        }
        if(!$isValid) $this->error(self::SHOP_ORDER_SN_EXISTS);

        return $isValid;
    }
}