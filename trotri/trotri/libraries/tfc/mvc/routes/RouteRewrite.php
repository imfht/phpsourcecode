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
 * RouteRewrite class file
 * 标准路由
 *
 * 一.标准路由例子：
 * URL：http://domain.com/author/iphper
 * <pre>
 * $route = new RouteRewrite(
 *     'author/:username',
 *     array(
 *         'controller' => 'profile',
 *         'action'     => 'userinfo'
 *     )
 * );
 * $value = array(
 *     'controller' => 'profile',
 *     'action'     => 'userinfo',
 *     'username'   => 'iphper'
 * );
 * </pre>
 *
 * 二.规则匹配：
 * URL：http://domain.com/archive/2012
 * <pre>
 * $route = new RouteRewrite(
 *     'archive/:year',
 *     array(
 *         'controller' => 'archive',
 *         'action'     => 'show'
 *     ),
 *     array(
 *         'year'    => '\d+'
 *     )
 * );
 * $value = array(
 *     'controller' => 'archive',
 *     'action'     => 'show',
 *     'year'       => '2012'
 * );
 * 如果URL：http://domain.com/archive/test，将不匹配，并且尝试匹配下一个路由
 * </pre>
 *
 * 三.有缺省值时，规则匹配：
 * URL：http://domain.com/archive
 * <pre>
 * $route = new RouteRewrite(
 *     'archive/:year',
 *     array(
 *         'year'       => '2012',    // 缺省值
 *         'controller' => 'archive',
 *         'action'     => 'show'
 *     )
 * );
 * $value = array(
 *     'year'       => '2012',
 *     'controller' => 'archive',
 *     'action'     => 'show'
 * );
 * </pre>
 *
 * 四.模仿模块路由：
 * URL：http://domain.com/passport/profile/userinfo/username/iphper
 * <pre>
 * $route = new RouteRewrite(
 *     ':module/:controller/:action/*',
 *     array(
 *        'module' => 'default'    // 缺省值
 *     )
 * );
 * $value = array(
 *     'module'     => 'passport',
 *     'controller' => 'profile',
 *     'action'     => 'userinfo',
 *     'username'   => 'iphper'
 * );
 * 如果URL：http://domain.com/archive/test，将不匹配，并且尝试匹配下一个路由
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: RouteRewrite.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc.routers
 * @since 1.0
 */
class RouteRewrite extends Route
{
    /**
     * @var string 正则分隔符
     */
    const REGEX_DELIMITER = '#';

    /**
     * @var string 变量前缀
     */
    protected $_urlVariable = ':';

    /**
     * @var string|null 默认正则
     */
    protected $_defaultRegex = null;

    /**
     * @var string 路由匹配规则
     */
    protected $_route = '';

    /**
     * @var array Path段匹配规则
     */
    protected $_regs = array();

    /**
     * 构造方法：初始化路由匹配规则、默认参数、Path段匹配规则
     * @param string $route
     * @param array $defaults
     * @param array $regs
     */
    public function __construct($route, array $defaults = array(), array $regs = array())
    {
        $this->_route = trim((string) $route, self::URI_DELIMITER . ' ');
        $this->_defaults = $defaults;
        $this->_regs = $regs;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\routes\Route::match()
     */
    public function match(HttpRequest $request)
    {
        if ($this->_route == '') {
            return false;
        }

        $needMapped = false;
        if (substr($this->_route, -1) == '*') {
            $needMapped = true;
            $this->_route = substr($this->_route, 0, -2);
        }

        $regs = $maps = $routes = array();
        foreach (explode(self::URI_DELIMITER, $this->_route) as $key => $value) {
            if (substr($value, 0, 1) == $this->_urlVariable) {
                $value = substr($value, 1);
                $maps[$key] = $value;
                $regs[$key] = (isset($this->_regs[$key]) ? $this->_regs[$key] : $this->_defaultRegex);
            }

            $routes[$key] = $value;
        }

        $bits = explode(self::URI_DELIMITER, trim($request->pathInfo, self::URI_DELIMITER));
        foreach ($bits as $key => $value) {
            if (!isset($routes[$key])) {
                break;
            }

            if (isset($maps[$key])) {
                if ($regs[$key] !== null
                    && !preg_match(self::REGEX_DELIMITER . '^' . $regs[$key] . '$' . self::REGEX_DELIMITER . 'iu', $value)) {
                    return false;
                }

                $this->_defaults[$maps[$key]] = $value;
                continue;
            }

            if ($routes[$key] !== $value) {
                return false;
            }

            unset($bits[$key]);
        }

        foreach ($this->_defaults as $key => $value) {
            if ($key === 'controller' || $key === 'action' || $key === 'module') {
                $method = 'set' . $key;
                $this->$method($value);
                continue;
            }

            if (is_int($key) && isset($maps[$key])) {
                $key = $maps[$key];
            }

            $request->setParam($key, $value);
        }

        // 取字符串':module/:controller/:action/*'中"*"后面的内容，填充request::params值
        if ($needMapped) {
            $bits = array_values($bits);
            $last = count($bits) - 1;
            if ($last > 0) {
                for ($pos = 0; $pos < $last; $pos += 2) {
                    $request->setParam($bits[$pos], $bits[$pos+1]);
                }
            }
        }

        return true;
    }
}
