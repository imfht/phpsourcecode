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

use Store\Entity\WarehouseGoods;
use Zend\Validator\AbstractValidator;

class SendOrderWarehouseValidator extends AbstractValidator
{
    const NOT_SCALAR            = 'notScalar';
    const WAREHOUSE_NOT_STOCK   = 'warehouseNotStock';

    protected $messageTemplates = [
        self::NOT_SCALAR            => "这不是一个标准输入值",
        self::WAREHOUSE_NOT_STOCK   => "库存不足，无法发货"
    ];

    private $entityManager;
    private $sendOrderGoods;

    public function __construct($options)
    {
        $this->entityManager    = $options['entityManager'];
        $this->sendOrderGoods   = $options['sendOrderGoods'];

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if (!is_array($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        if(empty($this->sendOrderGoods)) return false;

        foreach ($this->sendOrderGoods as $goodsValue) {
            if(!isset($value[$goodsValue->getGoodsId()])) {
                $this->error(self::WAREHOUSE_NOT_STOCK);
                return false;
            }
            $stockNum = $this->entityManager->getRepository(WarehouseGoods::class)->findMoreWarehouseGoodsNum($value[$goodsValue->getGoodsId()], $goodsValue->getGoodsId());
            if($goodsValue->getSalesGoodsSellNum() > $stockNum) {
                $this->error(self::WAREHOUSE_NOT_STOCK);
                return false;
            }
        }

        return true;
    }
}