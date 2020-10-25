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
 * PhoneValidator class file
 * 验证中国大陆手机号
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: PhoneValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.validator
 * @since 1.0
 */
class PhoneValidator extends Validator
{
    /**
     * @var string 正则：验证手机号
     */
    const REGEX_PHONE = '/^1\d{10}$/';

    /**
     * @var string 默认出错后的提醒消息
     */
    protected $_message = '"%value%" does not appear to be a valid phone.';

    /**
     * (non-PHPdoc)
     * @see \tfc\validator\Validator::isValid()
     */
    public function isValid()
    {
        return (preg_match(self::REGEX_PHONE, $this->getValue()) == $this->getOption());
    }
}
