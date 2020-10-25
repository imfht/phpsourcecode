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

use Store\Entity\Position;
use Zend\Validator\AbstractValidator;

class WarehousePositionCodeValidator extends AbstractValidator
{
    const NOT_SCALAR                        = 'notScalar';
    const WAREHOUSE_POSITION_CODE_EXISTS    = 'warehousePositionCodeExists';

    protected $messageTemplates = [
        self::NOT_SCALAR                        => "这不是一个标准输入值",
        self::WAREHOUSE_POSITION_CODE_EXISTS    => "该仓库号已经存在"
    ];

    private $entityManager;
    private $warehousePosition;

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['entityManager']))        $this->entityManager       = $options['entityManager'];
            if(isset($options['warehousePosition']))    $this->warehousePosition   = $options['warehousePosition'];
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $positionInfo = $this->entityManager->getRepository(Position::class)->findOneByPositionSn($value);

        if($this->warehousePosition == null) {
            $isValid = ($positionInfo==null);
        } else {
            if($this->warehousePosition->getPositionSn() != $value && $positionInfo != null)
                $isValid = false;
            else
                $isValid = true;
        }

        if(!$isValid) $this->error(self::WAREHOUSE_POSITION_CODE_EXISTS);

        return $isValid;
    }
}