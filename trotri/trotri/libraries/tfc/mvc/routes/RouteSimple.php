<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\mvc\routes;

use tfc\ap\HttpRequest;

/**
 * RouteSimple class file
 * 简单路由
 *
 * 简单路由例子：
 * URL：http://domain.com/index.php?c=archive&a=show
 * <pre>
 * $route = new RouteSimple('c', 'a', 'm');
 * $value = array(
 *     'module'     => '默认模型',
 *     'controller' => 'archive',
 *     'action'     => 'show'
 * );
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: RouteSimple.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc.routers
 * @since 1.0
 */
class RouteSimple extends Route
{
    /**
     * @var string 用于从Request中获取Action名的键
     */
    protected $_actionKey = 'a';

    /**
     * @var string 用于从Request中获取Controller名的键
     */
    protected $_controllerKey = 'c';

    /**
     * @var string 用于从Request中获取Module名的键
     */
    protected $_moduleKey = 'm';

    /**
     * 构造方法：初始化路用于从Request中获取Controller名、Action名和Module名的键
     * @param string $controllerKey
     * @param string $actionKey
     * @param string $moduleKey
     */
    public function __construct($controllerKey = null, $actionKey = null, $moduleKey = null)
    {
        if (is_string($controllerKey) && ($controllerKey = trim($controllerKey)) != '') {
            $this->_controllerKey = $controllerKey;
        }

        if (is_string($actionKey) && ($actionKey = trim($actionKey)) != '') {
            $this->_actionKey = $actionKey;
        }

        if (is_string($moduleKey) && ($moduleKey = trim($moduleKey)) != '') {
            $this->_moduleKey = $moduleKey;
        }
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\routes\Route::match()
     */
    public function match(HttpRequest $request)
    {
        $controller = $request->getParam($this->_controllerKey);
        $action = $request->getParam($this->_actionKey);
        $module = $request->getParam($this->_moduleKey);

        $this->setController($controller);
        $this->setAction($action);
        $this->setModule($module);

        return true;
    }
}
