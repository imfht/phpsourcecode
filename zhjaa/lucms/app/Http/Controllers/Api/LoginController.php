<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Api\Traits\ProxyTrait;
use App\Models\AdminUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class LoginController extends ApiController
{
    use AuthenticatesUsers, ProxyTrait;


    public function __construct()
    {
        $this->middleware('guest')->except('logout,adminUserLogout,refreshToken');
//        $this->middleware('ip-filter')->only('login');
    }

    public function login(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            $res_msg = $validator->errors()->first();
            return $this->failed($res_msg);
        }

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->failed('您登录的次数过多，无法再登录', 429);
        }

        $user = User::enable()
            ->where('email', $request->email)
            ->isAdminSearch('T')
            ->firstOrFail();

        if (!Hash::check($request->password, $user->password)) {
            return $this->failed('密码不正确');
        }

        $user->last_login_at = Carbon::now();
        $user->save();

        $return = $user->toArray();

        foreach ($user->roles as $role) {
            $return['roles'][] = $role['name'];
        }

        $tokens = $this->authenticate();
        return $this->success(['token' => $tokens, 'user' => $return]);
    }

    public function logout()
    {
//        if (\Auth::guard('api')->check()) {
////            \Auth::guard('api')->user()->token()->revoke();
//            \Auth::guard('api')->user()->token()->delete();
//        }

        return $this->message('退出登录成功');
    }

    public function adminUserLogin(Request $request)
    {

        $admin_user = AdminUser::where('email', $request->email)
            ->firstOrFail();

        if (!Hash::check($request->password, $admin_user->password)) {
            return $this->failed('密码不正确');
        }

        $admin_user->last_login_at = Carbon::now();
        $admin_user->save();

        $tokens = $this->authenticate('admin_users');
        return $this->success(['token' => $tokens, 'user' => $admin_user]);
    }

    public function adminUserLogout()
    {
        if (\Auth::guard('admin_user_api')->check()) {
//            \Auth::guard('admin_user_api')->user()->token()->revoke();
            \Auth::guard('admin_user_api')->user()->token()->delete();
        }

        return $this->message('退出登录成功');
    }

    public function refreshToken()
    {
        $tokens = $this->getRefreshtoken();
        return $this->success(['token' => $tokens]);
    }


    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => '请输入邮箱',
            'email.email' => '邮箱格式不正确',
            'password.required' => '请输入密码',
            'password.min' => '密码长度至少是6位',
        ]);
    }

}
