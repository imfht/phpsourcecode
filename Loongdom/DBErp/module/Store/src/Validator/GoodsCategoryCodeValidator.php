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

class GoodsCategoryCodeValidator extends AbstractValidator
{
    const NOT_SCALAR                 = 'notScalar';
    const GOODS_CATEGORY_CODE_EXISTS = 'goodsCategoryCodeExists';

    protected $messageTemplates = [
        self::NOT_SCALAR                    => "这不是一个标准输入值",
        self::GOODS_CATEGORY_CODE_EXISTS    => "分类编码已经存在"
    ];

    private $entityManager;
    private $category;

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))    $this->entityManager= $options['entityManager'];
            if(isset($options['category']))         $this->category     = $options['category'];
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $categoryInfo = $this->entityManager->getRepository(GoodsCategory::class)->findOneByGoodsCategoryCode($value);

        if($this->category == null) {
            $isValid = ($categoryInfo==null);
        } else {
            if($this->category->getGoodsCategoryCode() != $value && $categoryInfo != null)
                $isValid = false;
            else
                $isValid = true;
        }

        if(!$isValid) $this->error(self::GOODS_CATEGORY_CODE_EXISTS);

        return $isValid;
    }
}