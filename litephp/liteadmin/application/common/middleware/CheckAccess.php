<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/4
 * Time: 14:39
 */

namespace app\common\middleware;


use app\common\model\SystemAdmin;
use LiteAdmin\Auth;
use think\Loader;
use think\Request;

/**
 * 权限校验中间件
 * Class CheckAccess
 * @package app\common\middleware
 */
class CheckAccess
{
    public function handle(Request $request,\Closure $next)
    {
        $admin_id = session('admin.id');
        if (!!$admin_id){
            $admin = SystemAdmin::get($admin_id);
            if ($admin->getData('state') !== 1){
                error_response(401, '当前账户已被禁用');
            }
        }

        $module = $request->module();
        $controller = Loader::parseName($request->controller(),0);
        $action = $request->action();

        $path = "{$module}/{$controller}/{$action}";

        if (!Auth::auth($path)){
            error_response(403, '当前请求没有权限');
        }

        $response = $next($request);

        return $response;
    }


}