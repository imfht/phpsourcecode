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
 * DateTimeValidator class file
 * 验证日期时间
 * 日期时间需要大于1901-12-14 04:51:49
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DateTimeValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.validator
 * @since 1.0
 */
class DateTimeValidator extends Validator
{
    /**
     * @var string 正则：验证日期时间
     */
    protected $_format = 'Y-m-d H:i:s';

    /**
     * @var string 默认出错后的提醒消息
     */
    protected $_message = '"%value%" does not appear to be a valid date time.';

    /**
     * (non-PHPdoc)
     * @see \tfc\validator\Validator::isValid()
     */
    public function isValid()
    {
        return ($this->isDt($this->getValue()) == $this->getOption());
    }

    /**
     * 验证字符串是否是日期时间格式
     * @param string $value
     * @return boolean
     */
    public function isDt($value)
    {
        $timestamp = strtotime($value);
        return (date($this->_format, $timestamp) === $value);
    }
}
