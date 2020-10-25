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
 * RouteRegex class file
 * 正则路由
 *
 * 一.正则路由例子：
 * URL：http://domain.com/archive/2012
 * <pre>
 * $route = new RouteRegex(
 *     'archive/(\d+)',
 *     array(
 *         'controller' => 'archive',
 *         'action'     => 'show'
 *     )
 * );
 * $value = array(
 *     'controller' => 'archive',
 *     'action'     => 'show',
 *     1            => '2012'
 * );
 * </pre>
 *
 * 二.上面例子中整数键不容易管理，下面方式解决：
 * <pre>
 * $route = new RouteRegex(
 *     'archive/(\d+)',
 *     array(
 *         'controller' => 'archive',
 *         'action'     => 'show'
 *     ),
 *     array(
 *         1 => 'year'
 *     )
 * );
 * $value = array(
 *     'controller' => 'archive',
 *     'action'     => 'show',
 *     'year'       => '2012'
 * );
 * </pre>
 *
 * 三.如果URL的地址是：http://domain.com/archive，即后面缺省2012时，下面方式解决：
 * <pre>
 * $route = new RouteRegex(
 *     'archive(?:/(\d+))?',
 *     array(
 *         1            => '2012',
 *         'controller' => 'archive',
 *         'action'     => 'show'
 *     ),
 *     array(
 *         1 => 'year'
 *     )
 * );
 * $value = array(
 *     'controller' => 'archive',
 *     'action'     => 'show',
 *     'year'       => '2012'
 * );
 * </pre>
 *
 * 四.URL中有多个匹配：
 * URL：http://domain.com/iphper/page/8
 * <pre>
 * $route = new RouteRegex(
 *     '(\w+)/page/(\d+)',
 *     array(
 *         'controller' => 'profile',
 *         'action'     => 'userinfo'
 *     ),
 *     array(
 *         2 => 'page'
 *     )
 * );
 * $value = array(
 *     'controller' => 'profile',
 *     'action'     => 'userinfo',
 *     1            => 'iphper',
 *     'page'       => '8'
 * );
 * </pre>
 *
 * 五.URL中有多个参数：
 * URL：http://domain.com/archive/2012/username/iphper/page/2
 * <pre>
 * $route = new RouteRegex(
 *     'archive/(\d+)',
 *     array(
 *         'controller' => 'archive',
 *         'action'     => 'show'
 *     ),
 *     array(
 *         2 => 'page'
 *     )
 * );
 * $value = array(
 *     'controller' => 'archive',
 *     'action'     => 'show',
 *     1            => '2012',
 *     'username'   => 'iphper',
 *     'page'       => '2'
 * );
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: RouteRegex.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.mvc.routers
 * @since 1.0
 */
class RouteRegex extends Route
{
    /**
     * @var string 正则分隔符
     */
    const REGEX_DELIMITER = '#';

    /**
     * @var string 正则匹配规则
     */
    protected $_pattern;

    /**
     * @var array 数字键映射字母键
     */
    protected $_maps = array();

    /**
     * 构造方法：初始化正则匹配规则、默认参数、数字键映射字母键
     * @param string $pattern
     * @param array $defaults
     * @param array $maps
     */
    public function __construct($pattern, array $defaults = array(), array $maps = array())
    {
        $this->_pattern = trim($pattern, self::URI_DELIMITER);
        $this->_defaults = $defaults;
        $this->_maps = $maps;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\mvc\routes\Route::match()
     */
    public function match(HttpRequest $request)
    {
        $path = trim($request->pathInfo, self::URI_DELIMITER);
        $pattern = self::REGEX_DELIMITER . '^' . $this->_pattern . self::REGEX_DELIMITER . 'i';

        if (preg_match($pattern, $path, $regs) === 0) {
            return false;
        }

        $path = ltrim(substr($path, strlen($regs[0])), self::URI_DELIMITER);
        unset($regs[0]);
        foreach ($regs as $key => $value) {
            if (!is_int($key)) {
                unset($regs[$key]);
            }
        }

        foreach (array_merge($this->_defaults, $regs) as $key => $value) {
            if ($key === 'controller' || $key === 'action' || $key === 'module') {
                $method = 'set' . $key;
                $this->$method($value);
                continue;
            }

            if (is_int($key) && isset($this->_maps[$key])) {
                $key = $this->_maps[$key];
            }

            $request->setParam($key, $value);
        }

        $bits = explode(self::URI_DELIMITER, $path);
        $last = count($bits) - 1;
        if ($last > 0) {
            for ($pos = 0; $pos < $last; $pos += 2) {
                $request->setParam($bits[$pos], $bits[$pos+1]);
            }
        }

        return true;
    }
}
