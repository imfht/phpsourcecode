<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\mvc\form;

/**
 * ButtonElement class file
 * 按钮类表单元素
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: ButtonElement.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc.form
 * @since 1.0
 */
class ButtonElement extends Element
{
    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\form\Element::fetch()
     */
    public function fetch()
    {
        return $this->getInput();
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\form\Element::getInput()
     */
    public function getInput()
    {
        $type = $this->getType();
        return $this->getHtml()->$type($this->value, $this->getName(), $this->getAttributes());
    }
}
