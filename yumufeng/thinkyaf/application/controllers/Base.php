<?php
/**
 * Date: 2018\4\2 0002 11:26
 */

class BaseController extends Rest
{
    // 网站的userId
    public $user_id;
    /*网站的token*/
    protected $token;

    protected function checkLogin()
    {
        $authKey = $this->header('token');
        empty($authKey) && $this->response(\bean\ErrorCode::NOT_LOGIN, '登录已失效');
        $cache = cache('auth_' . $authKey);
        if (empty($authKey) || empty($cache)) {
            return $this->response(\bean\ErrorCode::NOT_LOGIN, '登录已失效');
        }
        $userInfo = \think\Db::name('user')->cache(\bean\CachePrefix::DB_AUTH_UID . $cache['uid'], \bean\CacheTime::LOGIN_TIME)->field('password', true)->where('uid', $cache['uid'])->find();
        if (!($userInfo) || ($userInfo['state'] > 0)) {
            return $this->fail('账号已被删除或禁用');
        }
        $this->token = $authKey;
        $this->user_id = $userInfo['uid'];
        cache('auth_' . $authKey, $userInfo, \bean\CacheTime::LOGIN_TIME);
    }
}