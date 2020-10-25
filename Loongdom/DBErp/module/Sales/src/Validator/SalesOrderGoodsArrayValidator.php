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

use Store\Entity\Goods;
use Zend\Validator\AbstractValidator;

class SalesOrderGoodsArrayValidator extends AbstractValidator
{
    const NOT_SCALAR            = 'notScalar';
    const GOODS_NOT_EXISTS      = 'goodsNotExists';
    const GOODS_PRICE_NOT_MINUS = 'goodsPriceNotMinus';
    const SALES_NUM_NOT_ZERO    = 'salesNumNotZero';
    const GOODS_AMOUNT_NOT_MINUS= 'goodsAmountNotMinus';
    const GOODS_TAX_NOT_MINUS   = 'goodsTaxNotMinus';

    private $entityManager;
    private $goodsField;

    protected $options = [
        'entityManager',
        'goodsField'
    ];

    protected $messageTemplates = [
        self::NOT_SCALAR            => "不能为空",
        self::GOODS_NOT_EXISTS      => "商品不存在",
        self::GOODS_PRICE_NOT_MINUS => "商品销售单价不能为负数",
        self::GOODS_TAX_NOT_MINUS   => "税金不能为负数",
        self::SALES_NUM_NOT_ZERO    => "销售数量不能小于等于0",
        self::GOODS_AMOUNT_NOT_MINUS=> "商品总价不能为负数"
    ];

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager'])) $this->entityManager = $options['entityManager'];
        }
        $this->goodsField = $options['goodsField'];

        parent::__construct($options);
    }

    public function isValid($value)
    {
        $isValid = true;

        if (!is_array($value) && !empty($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        switch ($this->goodsField) {
            case 'goodsId':
                foreach ($value as $item) {
                    $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsId($item);
                    if(!$goodsInfo) {
                        $this->error(self::GOODS_NOT_EXISTS);
                        return false;
                    }
                }
                break;

            case 'goodsPrice':
                $array = array_filter($value, function ($k) {return $k < 0; });
                if(!empty($array)) {
                    $this->error(self::GOODS_PRICE_NOT_MINUS);
                    return false;
                }
                break;

            case 'goodsSellNum':
                $array = array_filter($value, function ($k) { return $k <= 0; });
                if(!empty($array)) {
                    $this->error(self::SALES_NUM_NOT_ZERO);
                    return false;
                }
                break;

            case 'goodsTax':
                $array = array_filter($value, function ($k) {return $k < 0; });
                if(!empty($array)) {
                    $this->error(self::GOODS_PRICE_NOT_MINUS);
                    return false;
                }
                break;

            case 'goodsAmount':
                $array = array_filter($value, function ($k) {return $k < 0; });
                if(!empty($array)) {
                    $this->error(self::GOODS_AMOUNT_NOT_MINUS);
                    return false;
                }
                break;
        }

        return $isValid;
    }
}