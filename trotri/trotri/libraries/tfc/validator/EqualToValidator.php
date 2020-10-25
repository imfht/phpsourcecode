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

use tfc\ap\Ap;

/**
 * EqualToValidator class file
 * 验证值是否等于RGP中的值
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: EqualToValidator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.validator
 * @since 1.0
 */
class EqualToValidator extends Validator
{
    /**
     * @var string 默认出错后的提醒消息
     */
    protected $_message = '"%value%" is not equal to "%option%".';

    /**
     * (non-PHPdoc)
     * @see \tfc\validator\Validator::isValid()
     */
    public function isValid()
    {
        return ($this->getValue() === Ap::getRequest()->getParam($this->getOption()));
    }
}
