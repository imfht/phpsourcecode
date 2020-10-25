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
 * MinLengthValidator class file
 * 验证字符长度是否大于指定长度
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: MinLengthValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.validator
 * @since 1.0
 */
class MinLengthValidator extends Validator
{
    /**
     * @var string 默认出错后的提醒消息
     */
    protected $_message = '"%value%" is too short ("%option%" characters min).';

    /**
     * (non-PHPdoc)
     * @see \tfc\validator\Validator::isValid()
     */
    public function isValid()
    {
        return (strlen($this->getValue()) >= $this->getOption());
    }
}
