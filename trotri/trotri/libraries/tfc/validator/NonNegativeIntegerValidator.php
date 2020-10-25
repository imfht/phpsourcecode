<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\validator;

/**
 * NonNegativeIntegerValidator class file
 * 验证一个值是否是非负整型
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: NonNegativeIntegerValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.validator
 * @since 1.0
 */
class NonNegativeIntegerValidator extends Validator
{
    /**
     * @var string 默认出错后的提醒消息
     */
    protected $_message = '"%value%" is not a nonnegative integer.';

    /**
     * (non-PHPdoc)
     * @see \tfc\validator\Validator::isValid()
     */
    public function isValid()
    {
        if (is_numeric($this->getValue()) != $this->getOption()) {
            return false;
        }

        return ((int) $this->getValue() >= 0);
    }
}
