<?php
/**
 * 后台权限中间件
 * Class AdminAuth
 * @package app\http\middleware
 */

namespace app\http\middleware;

use app\common\library\Config;
use app\common\library\Rbac;
use think\Container;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use traits\controller\Jump;


class AdminAuth
{
    use Jump;

    public function handle($request, \Closure $next)
    {
        /**
         * 自定义配置项
         * 获取方式config('param.{field}')
         */
        Config::config();

        //ip限制
        if (config('param.admin_allow_ip')) {
            if (!in_array($request->ip(), explode(',', config('param.admin_allow_ip')))) {
                throw new HttpException(401, 'Forbidden');
            }
        }

        //注入用户
        app()->rbac = Rbac::instance();
        app()->user = Rbac::instance()->user();

        if (!Rbac::instance()->notNeedLogin()) {
            Rbac::instance()->user() || $this->redirect('public/login');
            Rbac::instance()->check() || $this->error('您无权限操作');
        }

        return $next($request);
    }
}
