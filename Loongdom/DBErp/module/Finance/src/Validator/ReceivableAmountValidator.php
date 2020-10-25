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

namespace Finance\Validator;

use Zend\Validator\AbstractValidator;

class ReceivableAmountValidator extends AbstractValidator
{
    const NOT_SCALAR    = 'notScalar';
    const AMOUNT_MORE   = 'amountMore';

    protected $options = [
        'receivableAmount'
    ];

    protected $messageTemplates = [
        self::NOT_SCALAR    => "这不是一个标准输入值",
        self::AMOUNT_MORE   => "收款值超过了应付值"
    ];

    public function __construct($options = null)
    {
        if(is_array($options)) {
            if(isset($options['receivableAmount']))  $this->options['receivableAmount'] = $options['receivableAmount'];
        }

        parent::__construct($options);
    }

    public function isValid($value)
    {
        if (!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        if($value > $this->options['receivableAmount']) {
            $this->error(self::AMOUNT_MORE);
            return false;
        }

        return true;
    }
}