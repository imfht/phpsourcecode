<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\mvc;

/**
 * InlineAction class file
 * 定义一个Action类，用来代替Controller类的Action方法
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: InlineAction.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc
 * @since 1.0
 */
class InlineAction extends Action
{
    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\interfaces\Action::run()
     */
    public function run()
    {
        $method = $this->getId() . 'Action';
        $this->getController()->$method();
    }
}
