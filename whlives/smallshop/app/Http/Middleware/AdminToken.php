<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/22
 * Time: 2:17 PM
 */

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\AdminRole;
use App\Services\TokenService;
use Closure;

class AdminToken
{
    public function handle($request, Closure $next)
    {
        $tokenService = new TokenService('admin');
        $token = $tokenService->getToken();
        if ($token) {
            $tokenService->refreshToken();
        } else {
            api_error(__('admin.invalid_token'));
        }
        $this->checkRole($token);
        return $next($request);
    }

    /**
     * 验证权限
     */
    public function checkRole($token)
    {
        $user_data = Admin::find($token['id']);
        if (!$user_data) {
            api_error(__('admin.invalid_token'));
        }
        //验证用户状态
        if ($user_data['status'] != Admin::STATUS_ON) {
            api_error(__('admin.user_freeze'));
        }
        $role_right = AdminRole::adminRight($user_data['role_id']);
        $url_path = request()->path();
        if (in_array($url_path, $role_right['menus']) || $user_data['role_id'] == 1) {
            return true;
        } else {
            api_error(__('admin.role_error'));
        }
    }
}