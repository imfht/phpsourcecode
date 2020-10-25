<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1;

use App\Libs\Weixin\MiniProgram;
use App\Libs\Weixin\Wechat;
use App\Models\LoginLog;
use App\Models\Member;
use App\Models\MemberAuth;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends BaseController
{
    public function __construct()
    {
        $this->token_service = new TokenService('api');
    }

    /**
     * 账号密码登陆
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function index(Request $request)
    {
        $username = $request->post('username');
        $password = $request->post('password');
        if (!check_mobile($username) || !$username || !$password) {
            api_error(__('api.missing_params'));
        }

        $member_data = Member::where('username', $username)->first();
        if (!$member_data) {
            api_error(__('api.password_error'));
        } elseif (!Hash::check($password, $member_data['password'])) {
            api_error(__('api.password_error'));
        } elseif ($member_data['status'] != Member::STATUS_ON) {
            api_error(__('api.user_freeze'));
        } else {
            $res = $this->loginSuccess($member_data);
            return $this->success($res);
        }
    }

    /**
     * 验证码登陆，没有注册的时候默认注册
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function speed(Request $request)
    {
        $mobile = $request->post('mobile');
        if (!check_mobile($mobile) || !$mobile) {
            api_error(__('api.missing_params'));
        }

        $member_data = Member::where('username', $mobile)->first();
        if (!$member_data) {
            //没有注册的直接注册
            $m_id = $this->register(['username' => $mobile]);
            if ($m_id) {
                $member_data = Member::find($m_id);
            } else {
                api_error(__('api.fail'));
            }
        }
        if ($member_data['status'] != Member::STATUS_ON) {
            api_error(__('api.user_freeze'));
        }
        $res = $this->loginSuccess($member_data);
        return $this->success($res);
    }

    /**
     * 第三方登陆
     * @param Request $request
     */
    public function auth(Request $request)
    {
        $type = $request->post('type');
        $union_id = $request->post('union_id');
        $openid = $request->post('openid');
        $nickname = $request->post('nickname');
        $headimg = $request->post('headimg');
        if (!$openid) $openid = $union_id;

        if (!$type || !$union_id || !$openid || !$nickname || !$headimg) {
            api_error(__('api.missing_params'));
        }
        if (!isset(MemberAuth::TYPE_DESC[$type])) {
            api_error(__('api.auth_type_error'));
        }
        $user_data = [
            'type' => $type,
            'union_id' => $union_id,
            'openid' => $openid,
            'nickname' => $nickname,
            'headimg' => $headimg
        ];
        return $this->authCheck($user_data);
    }

    /**
     * 小程序登陆
     * @param Request $request
     */
    public function miniProgram(Request $request)
    {
        $code = $request->post('code');
        $iv = $request->post('iv');
        $encrypt_data = $request->post('encrypt_data');
        if (!$code || !$iv || !$encrypt_data) {
            api_error(__('api.missing_params'));
        }
        $mini_program = new MiniProgram();
        $auth_info = $mini_program->decryptData($code, $iv, $encrypt_data);
        if (isset($auth_info['openId'])) {
            $union_id = isset($auth_info['unionId']) ? $auth_info['unionId'] : $auth_info['openId'];
            $user_data = [
                'type' => MemberAuth::TYPE_WECHAT,
                'union_id' => $union_id,
                'openid' => $auth_info['openId'],
                'nickname' => $auth_info['nickName'],
                'headimg' => $auth_info['avatarUrl']
            ];
            return $this->authCheck($user_data);
        } else {
            api_error(__('api.fail'));
        }
    }

    /**
     * 微信公众号、开放平台登陆
     * @param Request $request
     */
    public function wechat(Request $request)
    {
        $mp = new Wechat();
        $auth_info = $mp->userInfo();
        if (isset($auth_info['openid'])) {
            $union_id = isset($auth_info['unionid']) ? $auth_info['unionid'] : $auth_info['openid'];
            $user_data = [
                'type' => MemberAuth::TYPE_WECHAT,
                'union_id' => $union_id,
                'openid' => $auth_info['openid'],
                'nickname' => $auth_info['nickname'],
                'headimg' => $auth_info['headimgurl']
            ];
            return $this->authCheck($user_data);
        } else {
            api_error(__('api.fail'));
        }
    }

    /**
     * 第三方绑定手机
     * @param Request $request
     */
    public function bindMobile(Request $request)
    {
        $mobile = $request->post('mobile');
        if (!check_mobile($mobile) || !$mobile) {
            api_error(__('api.missing_params'));
        }
        return $this->checkBindMobile($mobile);
    }

    /**
     * 小程序绑定手机
     * @param Request $request
     */
    public function miniProgramBindMobile(Request $request)
    {
        $code = $request->post('code');
        $iv = $request->post('iv');
        $encrypt_data = $request->post('encrypt_data');

        if (!$code || !$iv || !$encrypt_data) {
            api_error(__('api.missing_params'));
        }
        $mini_program = new MiniProgram();
        $auth_info = $mini_program->decryptData($code, $iv, $encrypt_data);
        $mobile = $auth_info['purePhoneNumber'];
        if (!$mobile) {
            api_error(__('api.missing_params'));
        }
        return $this->checkBindMobile($mobile);
    }

    /**
     * 找回密码
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function findPassword(Request $request)
    {
        $mobile = $request->post('mobile');
        $password = $request->post('password');
        if (!$mobile || !$password) {
            api_error(__('api.missing_params'));
        }
        $update_data['password'] = Hash::make($password);
        $res = Member::where('username', $mobile)->update($update_data);
        if ($res) {
            return $this->success(true);
        } else {
            api_error(__('api.fail'));
        }
    }

    /**
     * 退出登陆
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function loginOut(Request $request)
    {
        $token_service = new TokenService('api');
        $token_service->getToken();
        return $this->success(true);
    }

    /**
     * 注册用户
     * @param $data
     */
    private function register($data)
    {
        $username = $data['username'];
        if (!$username) {
            api_error(__('api.missing_params'));
        }
        //如果不存在直接注册
        $member_data = [
            'username' => $username,
            'password' => isset($data['password']) ? $data['password'] : Str::random(10),
            'nickname' => isset($data['nickname']) ? $data['nickname'] : substr($username, 0, 3) . '****' . substr($username, -4, 4),
            'headimg' => isset($data['headimg']) ? $data['headimg'] : config('app.member_default_headimg'),
        ];
        $profile_data = array();
        $m_id = Member::saveData($member_data, $profile_data);
        if ($m_id) {
            return $m_id;
        }
        return false;
    }

    /**
     * 第三方登陆信息验证
     * @param $user_data 用户授权获取的信息
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    private function authCheck($user_data)
    {
        //查询账号是否已经存在
        $m_id = MemberAuth::where([['type', $user_data['type']], ['union_id', $user_data['union_id']]])->value('m_id');
        if ($m_id) {
            //查询用户信息
            $member_data = Member::find($m_id);
            if (!$member_data) {
                api_error(__('api.fail'));
            } elseif ($member_data['status'] != Member::STATUS_ON) {
                api_error(__('api.user_freeze'));
            } else {
                $member_data['openid'] = isset($user_data['openid']) ? $user_data['openid'] : '';//为了方便支付的时候获取openid，这里直接在登陆的时间存到token
                $res = $this->loginSuccess($member_data);
                return $this->success($res);
            }
        } else {
            //没有绑定的
            $user_data = [
                'type' => $user_data['type'],
                'union_id' => $user_data['union_id'],
                'openid' => $user_data['openid'],
                'nickname' => $user_data['nickname'],
                'headimg' => $user_data['headimg']
            ];
            cache(['app_auth_info:' . get_device() => $user_data], 600);
            $return = array(
                'id' => 0
            );
            return $this->success($return);
        }
    }

    /**
     * 第三方绑定手机验证
     * @param Request $request
     */
    private function checkBindMobile($mobile)
    {
        if (!check_mobile($mobile) || !$mobile) {
            api_error(__('api.missing_params'));
        }
        //获取第三方信息
        $auth_data = cache('app_auth_info:' . get_device());
        if (!$auth_data) {
            api_error(__('api.auth_data_error'));
        }

        $member_data = Member::where('username', $mobile)->first();
        if ($member_data) {
            //手机号已经注册，查询是否绑定了第三方
            if (!MemberAuth::where([['type', $auth_data['type']], ['m_id', $member_data['id']]])->exists()) {
                $member_auth_data = [
                    'type' => $auth_data['type'],
                    'union_id' => $auth_data['union_id'],
                    'openid' => $auth_data['openid'],
                    'm_id' => $member_data['id'],
                ];
                MemberAuth::create($member_auth_data);
            } else {
                api_error(__('api.user_mobile_is_bind'));
            }
        } else {
            //手机号还没注册过
            $member_insert_data = array(
                'username' => $mobile,
                'nickname' => $auth_data['nickname'],
                'headimg' => $auth_data['headimg']
            );
            $m_id = $this->register($member_insert_data);
            if ($m_id) {
                $member_auth_data = [
                    'type' => $auth_data['type'],
                    'union_id' => $auth_data['union_id'],
                    'openid' => $auth_data['openid'],
                    'm_id' => $m_id,
                ];
                $res = MemberAuth::create($member_auth_data);
                if ($res) {
                    $member_data = Member::find($m_id);
                } else {
                    api_error(__('api.fail'));
                }
            } else {
                api_error(__('api.fail'));
            }
        }
        $member_data['openid'] = isset($auth_data['openid']) ? $auth_data['openid'] : '';//为了方便支付的时候获取openid，这里直接在登陆的时间存到token
        cache(['app_auth_info:' . get_device() => false], 0);//删除保存的授权信息
        $res = $this->loginSuccess($member_data);
        return $this->success($res);
    }

    /**
     * 登陆成功操作
     * @param $data
     * @return bool|string
     */
    private function loginSuccess($data)
    {
        if (!$data['id']) {
            api_error(__('api.missing_params'));
        }
        $platform = get_platform();
        $token_data = array(
            'id' => $data['id'],
            'username' => $data['username'],
            'openid' => isset($data['openid']) ? $data['openid'] : ''
        );
        $token_data['device'] = get_device();
        $token_data['platform'] = $platform;
        $token_name = $this->token_service->setToken($token_data);
        if (!$token_name) {
            api_error(__('api.fail'));
        }
        //查询账号登陆记录并删除以前的token(限制单设备登陆)
        $login_log = LoginLog::select('id', 'token')->where([['m_id', $token_data['id']], ['platform', $platform], ['status', LoginLog::STATUS_ON]])->get();
        if (!$login_log->isEmpty()) {
            $log_ids = array();
            foreach ($login_log as $value) {
                $this->token_service->delToken($value['token']);
                $log_ids[] = $value['id'];
            }
            if ($log_ids) {
                LoginLog::whereIn('id', $log_ids)->update(['status' => LoginLog::STATUS_OFF]);
            }
        }
        //记录token到数据库
        $log = array(
            'token' => $token_name,
            'm_id' => $token_data['id'],
            'platform' => $platform
        );
        LoginLog::create($log);
        $return = array(
            'id' => $data['id'],
            'username' => $data['username'],
            'token' => $token_name
        );
        return $return;
    }
}
