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
 * InArrayValidator class file
 * 验证值在数组中是否存在
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: InArrayValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.validator
 * @since 1.0
 */
class InArrayValidator extends Validator
{
    /**
     * @var string 默认出错后的提醒消息
     */
    protected $_message = '"%value%" was not found in the haystack.';

    /**
     * (non-PHPdoc)
     * @see \tfc\validator\Validator::isValid()
     */
    public function isValid()
    {
        if (is_array($this->getValue())) {
            return (array_diff($this->getValue(), $this->getOption()) === array());
        }

        return in_array($this->getValue(), $this->getOption());
    }
}
