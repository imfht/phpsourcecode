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

use Store\Entity\Goods;
use Zend\Validator\AbstractValidator;

class GoodsCodeValidator extends AbstractValidator
{
    const NOT_SCALAR        = 'notScalar';
    const GOODS_CODE_EXISTS = 'goodsCodeExists';

    protected $messageTemplates = [
        self::NOT_SCALAR        => "这不是一个标准输入值",
        self::GOODS_CODE_EXISTS => "商品编码已经存在"
    ];

    private $entityManager;
    private $goods;

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->entityManager= $options['entityManager'];
            if(isset($options['goods']))            $this->goods        = $options['goods'];
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsNumber($value);

        if($this->goods == null) {
            $isValid = ($goodsInfo==null);
        } else {
            if($this->goods->getGoodsNumber() != $value && $goodsInfo != null)
                $isValid = false;
            else
                $isValid = true;
        }

        if(!$isValid) $this->error(self::GOODS_CODE_EXISTS);

        return $isValid;
    }
}