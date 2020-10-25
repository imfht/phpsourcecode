<?php
/**
 * @package     Version.php
 * @author      Jing Tang <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net/
 * @version     2.0
 * @copyright   Copyright (c) http://www.slimphp.net
 * @date        2017年8月8日
 */

namespace SlimCustom\Libs\Middlewares;

use Slim\Route;
use SlimCustom\Libs\Exception\NotFoundException;

/**
 * 接口版本控制
 * 
 * @author Jing Tang <tangjing3321@gmail.com>
 */
class Version
{
    
    /**
     * 版本号
     * 
     * @var string
     */
    private $version;
    
    /**
     * 接口版本处理
     * 
     * @param Request $request
     * @param Response $response
     * @param Route $next
     */
    public function resolve($request, $response, Route $next)
    {
        if ($this->getVersion()) {
            $next->setCallable($this->resolvePattern($next));
        }
        return $next($request, $response);
    }
    
    /**
     * 获取版本号
     * 
     * @return string
     */
    private function getVersion()
    {
        if (request()->hasHeader('api-version')) {
            $this->version = request()->getHeader('api-version')[0];
        }
        else {
            $this->version = request()->getParam('v');
        }
        return $this->version;
    }
    
    /**
     * resolvePattern
     * 
     * @param Route $next
     * @throws NotFoundException
     * @return string
     */
    private function resolvePattern(Route $next)
    {
        // 解析api
        list($class, $method) = explode(':', $next->getCallable());
        $class::initApiContentType();
        $key = "api.{$class}.version.{$this->version}";
        if (! $aimClass = cache()->get($key)) {
            $aimClass = str_replace($class::baseVersion(), $class::versionPrefix() . $this->version, $class);
            if (! class_exists($aimClass)) {
                goto checkApiAuth;
            }
            cache()->forever($key, $aimClass);
        }
        // 检查该版本接口权限
        checkApiAuth:
        if (! class_exists($aimClass)) {
            cache()->remove($key);
            throw new NotFoundException(null, 101);
        }
        return implode(':', [$aimClass, $method]);
    }
}