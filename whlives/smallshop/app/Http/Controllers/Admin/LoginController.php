<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/16
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class LoginController extends BaseController
{
    public function __construct()
    {
        $this->token_service = new TokenService('admin');
    }

    /**
     * 登陆
     * @param Request $request
     * @return array|void
     * @throws \App\Exceptions\ApiException
     */
    public function index(Request $request)
    {
        //验证规则Validator
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
            'captcha_code' => 'required',
            'captcha_key' => 'required',
        ], [
            'username.required' => '用户名不能为空',
            'password.required' => '密码不能为空',
            'captcha_code.required' => '验证码不能为空',
            'captcha_key.required' => '验证码不能为空',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            return api_error(current($error));
        }

        if (!captcha_api_check($request->input('captcha_code'), $request->input('captcha_key'))) {
            api_error(__('admin.captcha_error'));
        }

        $result = Admin::where('username', $request->input('username'))->first();
        if (!$result) {
            api_error(__('admin.admin_user_error'));
        } elseif (!Hash::check($request->input('password'), $result['password'])) {
            api_error(__('admin.admin_password_error'));
        } elseif ($result['status'] != Admin::STATUS_ON) {
            api_error(__('admin.admin_in_blacklist'));
        } else {
            $data = array(
                'id' => $result['id'],
                'username' => $result['username'],
                'role_id' => $result['role_id'],
            );
            $token = $this->token_service->setToken($data);
            //获取授权信息
            $domain = $_SERVER["HTTP_HOST"];
            $auth = curl('http://www.shop168.com.cn/wp-content/auth_site.php?domain=' . $domain);
            $auth = json_decode($auth, true);
            $return['access_token'] = $token;
            $return['auth_info'] = $auth;
            return $this->success($return);
        }
    }

    /**
     * 退出登陆
     * @param Request $request
     * @return array|void
     * @throws \App\Exceptions\ApiException
     */
    public function loginOut(Request $request)
    {
        $this->token_service->delToken();
        return $this->success();
    }
}
