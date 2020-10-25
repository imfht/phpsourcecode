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

use Store\Entity\GoodsCategory;
use Zend\Validator\AbstractValidator;

class GoodsCategoryValidator extends AbstractValidator
{
    const NOT_SCALAR    = 'notScalar';
    const NOT_SELF      = 'notSelf';
    const NOT_SUB       = 'notSub';

    protected $options = [
        'entityManager',
        'category'
    ];

    protected $messageTemplates = [
        self::NOT_SCALAR => "这不是一个标准输入值",
        self::NOT_SELF   => "不能为分类本身",
        self::NOT_SUB    => "不能为自己的下级分类"
    ];

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->options['entityManager'] = $options['entityManager'];
            if(isset($options['category']))         $this->options['category']      = $options['category'];
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        if($this->options['category'] == null || $value == 0) return true;

        if($this->options['category']->getGoodsCategoryId() == $value) {
            $this->error(self::NOT_SELF);
            return false;
        }

        $entityManager  = $this->options['entityManager'];
        $categoryInfo   = $entityManager->getRepository(GoodsCategory::class)->findOneByGoodsCategoryId($value);

        $num = strpos($categoryInfo->getGoodsCategoryPath(), (string) $this->options['category']->getGoodsCategoryId());
        if($num !== false) {
            $num1 = strpos($categoryInfo->getGoodsCategoryPath(), (string) $categoryInfo->getGoodsCategoryId());
            if($num < $num1) {
                $this->error(self::NOT_SUB);
                return false;
            }
        }

        return true;
    }
}