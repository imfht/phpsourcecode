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
 * AlphaValidator class file
 * 验证英文字母
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: AlphaValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.validator
 * @since 1.0
 */
class AlphaValidator extends Validator
{
    /**
     * @var string 正则：英文字母
     */
    const REGEX_ALPHA = '/^[a-z]+$/i';

    /**
     * @var string 默认出错后的提醒消息
     */
    protected $_message = '"%value%" does not appear to be a valid english alpha.';

    /**
     * (non-PHPdoc)
     * @see \tfc\validator\Validator::isValid()
     */
    public function isValid()
    {
        return (preg_match(self::REGEX_ALPHA, $this->getValue()) == $this->getOption());
    }
}
